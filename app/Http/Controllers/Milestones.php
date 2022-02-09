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

class Milestones extends Controller
{
    //add row 
    function Insert($data, $project_id, $final_proposal_id, $final_price)

    {

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
                if ($dividable == 0.0) {
                    $milestone_price = $this->calculatePrice($percentage, $final_price);
                    $mp = fmod($milestone_price, 5);

                    if ($mp == 0.0) {
                        $milestone_info = Milestone::create($arr + ["price" => $milestone_price]);

                        $Tasks->Insert($milestone['tasks'], $milestone_info->id);
                    } else {
                        return 102;
                    }
                } else {
                    return 101;
                }
            }
            return 200;
        } catch (Exception $error) {
            return 500;
        }
    }


    //update row according to row id
    function Update($id)
    {
    }
    //delete row according to row id
    function Delete($id)
    {
    }
    private function calculatePrice($percentage, $final_price)
    {

        $mPrice = $final_price * ($percentage / 100);
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
                $projectName=str_replace(' ', '-', $project->name);
                 $milestone = Milestone::where('id', $req->milestone_id)->select('name')->first();  
                $milestoneName=str_replace(' ', '-', $milestone->name);   
                $submissionName = time() . "-" . $projectName . '-' .$milestoneName.'-'. $req->file('submission_file')->getClientOriginalName();;
                $submission_file = $req->submission_file;
                if (!File::exists($projectName)) {
                    mkdir($projectName);
                    $submission_file->move(public_path($projectName), $submissionName);
                    $this->updateSubmissionFile($submission_id, $submissionName);
                    $this->updateStatus($req->milestone_id,'1');
                } else {
                    $submission_file->move(public_path($projectName), $submissionName);
                    $this->updateSubmissionFile($submission_id, $submissionName);
                    $this->updateStatus($req->milestone_id,'1');
                }
                $response = Controller::returnResponse(200, "submit successful",['submissionId'=>$submission_id]);
                return (json_encode($response));
            } catch (Exception $error) {
                $response = Controller::returnResponse(500, "Something went wrong", $error);
                return (json_encode($response));
            }
        }
    }
    function updateSubmissionFile($submission_id, $fileName)
    {
        milestone_submission::where('id', $submission_id)->update(array("file" => $fileName));
    }
    function updateStatus($id,$value)
    {
        Milestone::where('id',$id)->update(['status'=>$value]);
    }
}