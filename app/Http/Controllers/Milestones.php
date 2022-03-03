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
use App\Models\Final_proposal;
use Illuminate\Support\Facades\DB;

class Milestones extends Controller
{
    function Insert(Request $req)
    {
        try {
            $Tasks = new TasksController;
            $finalProposalObj = new Final_proposals;
            $rules = array(
                "team_id" => "required|exists:groups,id",
                "project_id" => "required|exists:projects,id",
                "milestone_num_hours" => "required",
                "milestone_price" => "required",
            );
            $validators = Validator::make($req->all(), $rules);
            if ($validators->fails()) {
                $responseData = $validators->errors();
                $response = Controller::returnResponse(101, "Validation Error", $responseData);
                return (json_encode($response));
            } else {
                $userData = Controller::checkUser($req);
                if ($userData['exist'] == 1) {
                    if ($userData['group_id'] == $req->team_id) {
                        if ($userData['privileges'] == 1) {
                            $finalProposal = $finalProposalObj->checkIfExists($req->project_id, $req->team_id);
                            if ($finalProposal['exist'] == 0) {
                                $new_final_proposal=$finalProposalObj->createEmptyFinalProposal($req->hourly_rate,$req->num_hours,$req->proposal_id,$req->team_id,$req->project_id,$userData['user_id']);
                                if($new_final_proposal['code']==422 ||$new_final_proposal['code']== 500)
                                {
                                    $response = Controller::returnResponse($new_final_proposal['code'], 'error generating final proposal', $new_final_proposal['msg']);
                                    return json_encode($response);
                                }
                                elseif($new_final_proposal['code'] == 200){
                                $data = array(
                                    "project_id" => $req->project_id,
                                    "final_proposal_id" => $new_final_proposal['msg'],
                                    "hours" => $req->milestone_num_hours,
                                    "price" => $req->milestone_price,
                                    "name" => $req->milestone_name,
                                    "description" => $req->milestone_description,
                                );
                               
                                $milestone = Milestone::create($data);
                                
                                $tasks = $Tasks->Insert($req->tasks, $milestone->id);
                                if ($tasks['code'] == 500) {
                                    $response = Controller::returnResponse(500, "something went wrong", $tasks['msg']);
                                    return (json_encode($response));
                                }
                                $response = Controller::returnResponse(200, "milestone added successfully", ["milestone_id" => $milestone->id]);
                                return (json_encode($response));
                            }
                            }
                             else {
                                $final_proposal_id = $finalProposal['final_proposal_id'];
                                $data = array(
                                    "project_id" => $req->project_id,
                                    "final_proposal_id" => $final_proposal_id,
                                    "hours" => $req->milestone_num_hours,
                                    "price" => $req->milestone_price,
                                    "name" => $req->milestone_name,
                                    "description" => $req->milestone_description,
                                );
                                
                                $milestone = Milestone::create($data);
                                
                                $tasks = $Tasks->Insert($req->tasks, $milestone->id);
                                if ($tasks['code'] == 500) {
                                    $response = Controller::returnResponse(500, "something went wrong", $tasks['msg']);
                                    return (json_encode($response));
                                }
                                $response = Controller::returnResponse(200, "milestone added successfully", ["milestone_id" => $milestone->id]);
                                return (json_encode($response));
                            }
                        } else {
                            $response = Controller::returnResponse(422, "Unauthorized action this action for admins", []);
                            return (json_encode($response));
                        }
                    } else {
                        $response = Controller::returnResponse(422, "Unauthorized you are trying to access another agency proposal", []);
                        return (json_encode($response));
                    }
                } else {
                    $response = Controller::returnResponse(422, "this user does not have team", []);
                    return (json_encode($response));
                }
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong milestones controller", $error->getMessage());
            return (json_encode($response));
        }
    }

    function updateMilestone(Request $req)
    {
        try {
            $finalProposalObj = new Final_proposals;
            $userData = Controller::checkUser($req);

            if ($userData['exist'] == 1) {
                if ($userData['group_id'] == $req->team_id) {
                    if ($userData['privileges'] == 1) {
                        $finalProposal = $finalProposalObj->checkIfExists($req->project_id, $req->team_id);
                        if ($finalProposal['exist'] == 0) {
                            $response = Controller::returnResponse(422, "you do not have final proposal ", []);
                            return (json_encode($response));
                        } else {
                            $Tasks = new TasksController;
                            $milestone = Milestone::where('id', $req->milestone_id)
                                ->update([
                                    'name' => $req->milestone_name, 'hours' => $req->milestone_num_hours, 'price' => $req->milestone_price,
                                    'description' => $req->milestone_description,
                                ]);
                            $delTasks = $Tasks->deleteTasksByMilestoneId($req->milestone_id);
                            $addTasks = $Tasks->Insert($req->tasks, $req->milestone_id);
                            if ($addTasks['code'] == 500) {
                                $response = Controller::returnResponse(500, "something went wrong ", $addTasks['msg']);
                                return (json_encode($response));
                            }
                            if ($delTasks['code'] == 500) {
                                $response = Controller::returnResponse(500, "something went wrong ", $delTasks['msg']);
                                return (json_encode($response));
                            }
                            $response = Controller::returnResponse(200, "milestone updated successful", []);
                            return (json_encode($response));
                        }
                    } else {
                        $response = Controller::returnResponse(422, "Unauthorized action this action for admins", []);
                        return (json_encode($response));
                    }
                } else {
                    $response = Controller::returnResponse(422, "Unauthorized you are trying to access another agency proposal", []);
                    return (json_encode($response));
                }
            } else {
                $response = Controller::returnResponse(422, "this user does not have team", []);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong ", $error->getMessage());
            return (json_encode($response));
        }
    }

    function deleteMilestone(Request $req)
    {
        try {
            $finalProposalObj = new Final_proposals;
            $userData = Controller::checkUser($req);
            if ($userData['exist'] == 1) {
                if ($userData['group_id'] == $req->team_id) {
                    if ($userData['privileges'] == 1) {
                        $finalProposal = $finalProposalObj->checkIfExists($req->project_id, $req->team_id);
                        if ($finalProposal['exist'] == 0) {
                            $response = Controller::returnResponse(422, "you do not have final proposal ", []);
                            return (json_encode($response));
                        } else {
                            $Tasks = new TasksController;
                            $delTasks = $Tasks->deleteTasksByMilestoneId($req->milestone_id);
                            if ($delTasks['code'] == 500) {
                                $response = Controller::returnResponse(500, "something went wrong ", $delTasks['msg']);
                                return (json_encode($response));
                            } elseif ($delTasks['code'] == 200 || $delTasks['code'] == 201) {
                                $milestone = Milestone::where('id', $req->milestone_id)->delete();
                                $response = Controller::returnResponse(200, "milestone deleted successful", []);
                                return (json_encode($response));
                            }
                        }
                    } else {
                        $response = Controller::returnResponse(422, "Unauthorized action this action for admins", []);
                        return (json_encode($response));
                    }
                } else {
                    $response = Controller::returnResponse(422, "Unauthorized you are trying to access another agency proposal", []);
                    return (json_encode($response));
                }
            } else {
                $response = Controller::returnResponse(422, "this user does not have team", []);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong ", $error->getMessage());
            return (json_encode($response));
        }
    }
    private function selectQuery($id)
    {
        $milestones = Milestone::where('id', $id)
            ->get()
            ->makeHidden(['created_at', 'updated_at']);
        return $milestones;
    }
    function getMilestones(Request $req, $id)
    {
        try {
            $finalProposalObj = new Final_proposals;
            $Tasks = new TasksController;
            $userData = Controller::checkUser($req);
            if ($userData['exist'] == 1) {
                $finalProposal = $finalProposalObj->getProposalById($id);
                if ($userData['group_id'] == $finalProposal['team_id']) {
                    $milestones =  Milestone::where('final_proposal_id', $id)
                        ->get()
                        ->makeHidden(['created_at', 'updated_at']);
                    $milestones_details = [];
                    foreach ($milestones as $milestone) {
                        $tasks = $Tasks->getTaskByMilestoneId($milestone->id);
                        array_push($milestones_details, array(
                            "milestone_id" => $milestone->id,
                            "milestone_name" => $milestone->name,
                            "milestone_description" => $milestone->description,
                            "milestone_price" => $milestone->price,
                            "milestone_num_hours" => $milestone->hours,
                            "tasks" => ($tasks),

                        ));
                    }
                    $response = Controller::returnResponse(200, "successful", $milestones_details);
                    return (json_encode($response));
                } else {
                    $response = Controller::returnResponse(422, "you are trying to access data for another agency", []);
                    return (json_encode($response));
                }
            } else {
                $response = Controller::returnResponse(422, "this user does not have team", []);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong ", $error->getMessage());
            return (json_encode($response));
        }
    } 
    function getMilestoneByProposalId($id)
    {
        try {
            $Tasks = new TasksController;
                    $milestones =  Milestone::where('final_proposal_id', $id)
                        ->get()
                        ->makeHidden(['created_at', 'updated_at']);
                    $milestones_details = [];
                    foreach ($milestones as $milestone) {
                        // $tasks = $Tasks->getTaskByMilestoneId($milestone->id);
                        array_push($milestones_details, array(
                            "milestone_id" => $milestone->id,
                            "milestone_name" => $milestone->name,
                            "milestone_description" => $milestone->description,
                            "milestone_price" => $milestone->price,
                            "milestone_hours" => $milestone->hours,
                            "milestone_status" => $milestone->status,
                            // "tasks" => ($tasks),
                        ));
                    }
                    return $milestones_details;
        } catch (Exception $error) {
           
            return[500,$error->getMessage()];
        }
    }
    function submitMilestone(Request $req)
    {
        try {

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
            } 
        }
        catch (Exception $error) {
            $response = Controller::returnResponse(500, "Something went wrong", $error->getMessage());
            return (json_encode($response));
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
}
