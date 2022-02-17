<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Milestone;
use Exception;
use Illuminate\Foundation\Mix;
use App\Models\milestone_submission;
use App\Models\Project;

use App\Http\Controllers\TasksController;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\GroupMembersController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\Final_proposals;
use Illuminate\Support\Facades\DB;

class Milestones extends Controller
{
    //add row 
    function Insert($data, $project_id, $final_proposal_id, $final_price)

    {
    //    dd(count($data));
        $Tasks = new TasksController;
      
        try {
            foreach ($data as $milestone) {
              
                $arr = array(
                    "project_id" => $project_id,
                    "final_proposal_id" => $final_proposal_id,
                    "name" => $milestone['name'],
                    "days" => $milestone['days'],
                    "description" => $milestone['description'],
                    "percentage" => $milestone['percentage'],
                );
                $percentage = $milestone['percentage'];
                $dividable = fmod($percentage, 5);
              
                if ($dividable == 0) {
                    $milestone_price = $this->calculatePrice($percentage, $final_price);
                    // $mp = fmod($milestone_price, 5);

                    // if ($mp == 0.0) {
                    $milestone_info = Milestone::create($arr + ["price" => $milestone_price]);

                    $Tasks->Insert($milestone['tasks'], $milestone_info->id);
                    // } 
                } else {
                    return ["code"=>'101','msg'=>'Milestone Validation error'];
                }
            }
            return 200;
        } catch (Exception $error) {
            return ["code"=>'500','msg'=>$error->getMessage()];
        }
    }


    //update row according to row id
    function Update(Request $req)
    {
        $rules = [
            "milestone_id" => "required|exists:milestones,id",
            'name' => "required|max:255",
            'description' => "required",
            'days' => "required",
            'percentage' => "required",

        ];
        $validators = Validator::make($req->all(), $rules);
        if ($validators->fails()) {
            $responseData = $validators->errors();
            $response = Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        }
        $Tasks = new TasksController;
        $userData = $req->user();
        $groupMemberObj = new GroupMembersController;
        $userPrivileges = $groupMemberObj->getUserPrivileges($userData->id);
        if ($userPrivileges->privileges != 1) {
            $response = Controller::returnResponse(101, "This function for admins only", []);
            return (json_encode($response));
        } else {
            try {
                $id = $req->milestone_id;
                $groupObj = new GroupController;
                $proposalObj = new Final_proposals;
                $group_id = $groupObj->getGroupIdByUserId($userData->id); //the user who try to dele this milestone
                $milestone_info = Milestone::where('id', $id)->select('final_proposal_id', 'name')->first();
                $proposal_info = $proposalObj->getProposalById($milestone_info->final_proposal_id);
                $team_id = $proposal_info['team_id']; //the team how has this milestone
                if ($group_id == $team_id) {
                    $percentage = $req->percentage;
                    $dividable = fmod($percentage, 5);
                    if ($dividable == 0.0) {
                        $milestone_price = $this->calculatePrice($percentage, $proposal_info->price);
                        $milestone = Milestone::where('id', $id)->update([
                            'name' => $req->name,
                             'description' => $req->description,
                            'days' => $req->days, 
                            'percentage' => $req->percentage,
                            'price' => $milestone_price,
                        ]);
                  
                        $Tasks->updateTasksByMileStoneId($req->tasks, $id);
                    } else {
                        $response = Controller::returnResponse(101, "the milestone percentage  should be multiples of 5", []);
                    return (json_encode($response));
                    }
                   
                    $response = Controller::returnResponse(200, "Update milestone: " . $milestone_info->name . ' successfully', []);
                    return (json_encode($response));
                } else {
                    $response = Controller::returnResponse(101, "The milestone you trying to update does not belong to your proposal", []);
                    return (json_encode($response));
                }
            } catch (Exception $error) {
                $response = Controller::returnResponse(500, "Something went wrong", $error->getMessage());
                return (json_encode($response));
            }
        }
    }
    //delete row according to row id
    function Delete(Request $req, $id)
    {
        $userData = $req->user();
        $groupMemberObj = new GroupMembersController;
        $userPrivileges = $groupMemberObj->getUserPrivileges($userData->id);

        if ($userPrivileges->privileges != 1) {
            $response = Controller::returnResponse(101, "This function for admins only", []);
            return (json_encode($response));
        } else {
            try {
                $groupObj = new GroupController;
                $proposalObj = new Final_proposals;
                $group_id = $groupObj->getGroupIdByUserId($userData->id); //the user who try to dele this milestone
                $milestone_info = Milestone::where('id', $id)->select('final_proposal_id', 'name')->first();

                $proposal_info = $proposalObj->getProposalById($milestone_info->final_proposal_id);
                $team_id = $proposal_info['team_id']; //the team how has this milestone

                if ($group_id == $team_id) {
                    $milestone = Milestone::where('id', $id)->delete();
                    $response = Controller::returnResponse(200, "Delete milestone: " . $milestone_info->name . ' successfully', []);
                    return (json_encode($response));
                } else {
                    $response = Controller::returnResponse(101, "The milestone you trying to delete does not belong to your proposal", []);
                    return (json_encode($response));
                }
            } catch (Exception $error) {
                $response = Controller::returnResponse(500, "Something went wrong", $error->getMessage());
                return (json_encode($response));
            }
        }
    }
    private function calculatePrice($percentage, $final_price)
    {

        $mPrice = $final_price * ($percentage / 100);
        $mPrice=number_format($mPrice,2);
        return $mPrice;
    }
    private function dividableBy5($number)
    {
        $mod = $number % 5;
        if ($mod == 0) {
            return 1;
        } else {
            return 0;
        }
    }
    function getMilestoneByProposalId($id)
    {
        $Tasks = new TasksController;
        $milestones = Milestone::select('id', 'project_id', 'final_proposal_id', 'name', 'description', 'days', 'percentage', 'price', 'status')
            ->where('final_proposal_id', $id)->get();
        $milestones_details = [];

        foreach ($milestones as $milestone) {

            $tasks = $Tasks->getTaskByMilestoneId($milestone->id);
            array_push($milestones_details, array(
                "milestone_id" => $milestone->id,
                "milestone_name" => $milestone->name,
                "milestone_description" => $milestone->description,
                "percentage" => $milestone->percentage,
                "price" => $milestone->price,
                "tasks" => ($tasks),

            ));
        }
        return ($milestones_details);
    }
    function submitMilestone(Request $req)
    {

        $rules = [
            "submission_file" => "mimes:zip,rar |max:35000",
            'comment' => "required",
            'project_id' => "required|exists:projects,id",
            'milestone_id' => "required|exists:milestones,id"
        ];
        $validators = Validator::make($req->all(), $rules);
        if ($validators->fails()) {
            $responseData = $validators->errors();
            $response = Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        } else {
            try {
                if (isset($req->links)) {
                    $links = serialize($req->links);
                } else {
                    $links = serialize(array());
                }
                $submission = milestone_submission::create($req->except(['submission_file']) + ['links' => $links]);
                $submission_id = $submission->id;
                $project = Project::where('id', $req->project_id)->select('name')->first();
                $projectName = str_replace(' ', '-', $project->name);
                $milestone = Milestone::where('id', $req->milestone_id)->select('name')->first();
                $milestoneName = str_replace(' ', '-', $milestone->name);
                $submissionName = time() . "-" . $projectName . '-' . $milestoneName . '-' . $req->file('submission_file')->getClientOriginalName();;
                $submission_file = $req->submission_file;
                if (!File::exists($projectName)) {
                    mkdir($projectName);
                    $submission_file->move(public_path($projectName), $submissionName);
                    $this->updateSubmissionFile($submission_id, $submissionName);
                    $this->updateStatus($req->milestone_id, '1');
                } else {
                    $submission_file->move(public_path($projectName), $submissionName);
                    $this->updateSubmissionFile($submission_id, $submissionName);
                    $this->updateStatus($req->milestone_id, '1');
                }
                $response = Controller::returnResponse(200, "submit successful", ['submissionId' => $submission_id]);
                return (json_encode($response));
            } catch (Exception $error) {
                $response = Controller::returnResponse(500, "Something went wrong", $error->getMessage());
                return (json_encode($response));
            }
        }
    }
    function updateSubmissionFile($submission_id, $fileName)
    {
        milestone_submission::where('id', $submission_id)->update(array("file" => $fileName));
    }
    function updateStatus($id, $value)
    {
        Milestone::where('id', $id)->update(['status' => $value]);
    }
    function deleteMilestonesByProposalId($id)

    {   
        $tasksObj=new TasksController;
        $milestone_ids=DB::table('milestones')
       -> where('final_proposal_id',$id)->select('id')->get();
       if($milestone_ids->isEmpty())
       {
           
       }
       else{ 
        foreach($milestone_ids as $milestone_id)
        {   
           
        $tasksObj->deleteTasksByMilestoneId($milestone_id->id);
        Milestone::where('id',$milestone_id->id)->delete();
        }
    }
        
    }
    function getDownPaymentByProposalId($id)
    {
        $downPayment=DB::table('milestones')
        ->where('final_proposal_id',"=",$id)
        ->where('description',"=","down payment")
        ->select('name','description','percentage','price')
        ->get();
        return($downPayment);
    }
}
