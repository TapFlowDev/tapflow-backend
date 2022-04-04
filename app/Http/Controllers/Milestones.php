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
use Money\Exchange;

class Milestones extends Controller
{
    function Insert(Request $req)
    {

        try {

            $finalProposalObj = new Final_proposals;
            $rules = array(
                "team_id" => "required|exists:groups,id",
                "project_id" => "required|exists:projects,id",
                "milestone_num_hours" => "required",
                "milestone_price" => "required",
                "deliverables" => "required",
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
                            $deliverables = [];
                            if ($finalProposal['exist'] == 0) {
                                $new_final_proposal = $finalProposalObj->createEmptyFinalProposal($req->hourly_rate, $req->hours, $req->proposal_id, $req->team_id, $req->project_id, $userData['user_id'], $req->type);
                                if ($new_final_proposal['code'] == 422 || $new_final_proposal['code'] == 500) {
                                    $response = Controller::returnResponse($new_final_proposal['code'], 'error generating final proposal', $new_final_proposal['msg']);
                                    return json_encode($response);
                                } elseif ($new_final_proposal['code'] == 200) {

                                    if (count($req->deliverables) >= 0) {
                                        $deliverables = serialize($req->deliverables);
                                    }
                                    $price = $this->calculatePrice($req->milestone_num_hours, $req->hourly_rate);
                                    $req['milestone_price'] = $price;
                                    $countDeliverables = "";
                                    if (count($req->deliverables) > 0) {
                                        $countDeliverables = "1";
                                    }
                                    $isValidArray = array($req->milestone_name, $req->milestone_description, $countDeliverables, $price, $req->milestone_num_hours);
                                    $isValid = $this->checkIsValid($isValidArray);
                                    $data = array(
                                        "project_id" => $req->project_id,
                                        "final_proposal_id" => $new_final_proposal['msg'],
                                        "hours" => $req->milestone_num_hours,
                                        "price" => $price,
                                        "name" => $req->milestone_name,
                                        "description" => $req->milestone_description,
                                        "deliverables" => serialize($req->deliverables),
                                        "is_valid" => $isValid
                                    );

                                    $milestone = Milestone::create($data);

                                    $response = Controller::returnResponse(200, "milestone added successfully", ["milestone_id" => $milestone->id]);
                                    return (json_encode($response));
                                }
                            } else {
                                if ($finalProposal['status'] == -1 || $finalProposal['status'] == 3) {
                                    $final_proposal_id = $finalProposal['final_proposal_id'];
                                    if (count($req->deliverables) >= 0) {
                                        $deliverables = $req->deliverables;
                                    }
                                    $price = $this->calculatePrice($req->milestone_num_hours, $req->hourly_rate);
                                    $req['milestone_price'] = $price;
                                    $countDeliverables = "";
                                    if (count($req->deliverables) > 0) {
                                        $countDeliverables = "1";
                                    }
                                    $isValidArray = array($req->milestone_name, $req->milestone_description, $countDeliverables, $price, $req->milestone_num_hours);
                                    $isValid = $this->checkIsValid($isValidArray);
                                    $data = array(
                                        "project_id" => $req->project_id,
                                        "final_proposal_id" => $final_proposal_id,
                                        "hours" => $req->milestone_num_hours,
                                        "price" => $req->milestone_price,
                                        "name" => $req->milestone_name,
                                        "description" => $req->milestone_description,
                                        "deliverables" => serialize($req->deliverables),
                                        "is_valid" => $isValid
                                    );
                                    if ($finalProposal['type'] == 1) {
                                        $MP = $this->updateMilestonesPrices($req->hours, $req->hourly_rate, $finalProposal['final_proposal_id']);
                                        if ($MP['code'] == 500) {
                                            $response = Controller::returnResponse(500, "something wrong update prices", $MP['msg']);
                                            return (json_encode($response));
                                        }
                                    } elseif ($finalProposal['type'] == 2) {
                                        $MP = $this->updateMilestonesMonthly($req->hours, $req->hourly_rate, $finalProposal['final_proposal_id']);
                                        if ($MP['code'] == 500) {
                                            $response = Controller::returnResponse(500, "something wrong update prices", $MP['msg']);
                                            return (json_encode($response));
                                        }
                                    }
                                    $milestone = Milestone::create($data);
                                    // $FP=Final_proposal::where('id',$finalProposal['final_proposal_id'])->update('')
                                    $response = Controller::returnResponse(200, "milestone added successfully", ["milestone_id" => $milestone->id]);
                                    return (json_encode($response));
                                } else {
                                    $response = Controller::returnResponse(422, "Uou already submit your proposal", []);
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
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong milestones controller", $error->getMessage());
            return (json_encode($response));
        }
    }

    function updateMilestone(Request $req)
    {
        // try {
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
                        $price = $this->calculatePrice($req->milestone_num_hours, $req->hourly_rate);

                        $req['milestone_price'] = $price;

                        $update = $this->milestoneDownPaymentHandler($req);
                        if ($update['update'] == 1) {
                            if ($finalProposal['type'] == 1) {
                                $MP = $this->updateMilestonesPrices($req->hours, $req->hourly_rate, $finalProposal['final_proposal_id']);
                                if ($MP['code'] == 500) {
                                    $response = Controller::returnResponse(500, "something wrong update prices", $MP['msg']);
                                    return (json_encode($response));
                                }
                            } elseif ($finalProposal['type'] == 2) {
                                $MP = $this->updateMilestonesMonthly($req->hours, $req->hourly_rate, $finalProposal['final_proposal_id']);
                                if ($MP['code'] == 500) {
                                    $response = Controller::returnResponse(500, "something wrong update prices", $MP['msg']);
                                    return (json_encode($response));
                                }
                            }

                            $countDeliverables = "";
                            if (count($req->deliverables) > 0) {
                                $countDeliverables = "1";
                            }
                            $isValidArray = array($req->milestone_name, $req->milestone_description, $countDeliverables, $price, $req->milestone_num_hours);
                            $isValid = $this->checkIsValid($isValidArray);
                            $milestone = Milestone::where('id', $req->milestone_id)
                                ->update([
                                    'name' => $req->milestone_name, 'hours' => $req->milestone_num_hours, 'price' => $req->milestone_price,
                                    'description' => $req->milestone_description, 'deliverables' => serialize($req->deliverables), 'is_valid' => $isValid
                                ]);
                            $response = Controller::returnResponse(200, "milestone updated successful", []);
                            return (json_encode($response));
                        } else {
                            $response = Controller::returnResponse(500, "something wrong down payment handler", $update['msg']);
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
        // } catch (Exception $error) {
        //     $response = Controller::returnResponse(500, "something went wrong ", $error->getMessage());
        //     return (json_encode($response));
        // }
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
                            $del = $this->downPaymentDelete($req->milestone_id);
                            if ($del['delete'] == 1) {
                                $milestone = Milestone::where('id', $req->milestone_id)->delete();
                                $response = Controller::returnResponse(200, "milestone deleted successful", []);
                                return (json_encode($response));
                            } else {
                                $response = Controller::returnResponse(500, "something wrong down payment handler", $del['msg']);
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

            $userData = Controller::checkUser($req);
            if ($userData['exist'] == 1) {
                $finalProposal = $finalProposalObj->getProposalById($id);
                if ($userData['group_id'] == $finalProposal['team_id']) {
                    $milestones =  Milestone::where('final_proposal_id', $id)
                        ->get()
                        ->makeHidden(['created_at', 'updated_at']);
                    $milestones_details = [];
                    foreach ($milestones as $milestone) {

                        array_push($milestones_details, array(
                            "milestone_id" => $milestone->id,
                            "milestone_name" => $milestone->name,
                            "milestone_description" => $milestone->description,
                            "milestone_price" => $milestone->price,
                            "milestone_num_hours" => $milestone->hours,
                            "milestone_down_payment" => $milestone->down_payment,
                            "deliverables" => unserialize($milestone->deliverables),
                            "isValid" => $milestone->is_valid
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

            $milestones =  Milestone::where('final_proposal_id', $id)
                ->get()
                ->makeHidden(['created_at', 'updated_at']);
            $milestones_details = [];
            foreach ($milestones as $milestone) {
                array_push($milestones_details, array(
                    "milestone_id" => $milestone->id,
                    "milestone_name" => $milestone->name,
                    "milestone_description" => $milestone->description,
                    "milestone_price" => $milestone->price,
                    "milestone_hours" => $milestone->hours,
                    "milestone_status" => $milestone->status,
                    "milestone_isPaid" => $milestone->is_paid,
                    "deliverables" => unserialize($milestone->deliverables),
                ));
            }
            return $milestones_details;
        } catch (Exception $error) {

            return [500, $error->getMessage()];
        }
    }
    function submitMilestone(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            if ($userData['exist'] == 1) {
                if ($userData['privileges'] == 1) {
                    if ($userData['group_id'] == $req->team_id) {
                        $rules = [
                            "submission_file" => "file|mimes:zip,rar|max:35000",
                            'agency_comments' => "required",
                            'project_id' => "required|exists:projects,id",
                            'milestone_id' => "required|exists:milestones,id"
                        ];
                        $validators = Validator::make($req->all(), $rules);
                        if ($validators->fails()) {
                            $responseData = $validators->errors();
                            $response = Controller::returnResponse(101, "Validation Error", $responseData);
                            return (json_encode($response));
                        } else {
                            $project_id=$req->project_id;
                            $submission = milestone_submission::create($req->except(['submission_file']));
                            $submission_id = $submission->id;                          
                            $milestone = Milestone::where('id', $req->milestone_id)->select('name')->first();
                            $milestoneName = str_replace(' ', '-', $milestone->name);
                            $originalName = str_replace(' ', '-',  $req->file('submission_file')->getClientOriginalName());
                            $submissionName = time() . '_' . $milestoneName . '_' . $originalName;
                            $submission_file = $req->submission_file;
                            if (!File::exists($project_id)) {
                                File::makeDirectory(public_path() . '/submissions/' . $project_id, 0777, true);
                                $submission_file->move(public_path($project_id), $submissionName);
                                $this->updateSubmissionFile($submission_id, $submissionName);
                                $this->updateStatus($req->milestone_id, '1');
                            } else {
                                $submission_file->move(public_path('/submissions/'.$project_id), $submissionName);
                                $this->updateSubmissionFile($submission_id, $submissionName);
                                $this->updateStatus($req->milestone_id, '1');
                            }
                            $response = Controller::returnResponse(200, "submit successful", ['submissionId' => $submission_id]);
                            return (json_encode($response));
                        }
                    } else {
                        $response = Controller::returnResponse(422, "Unauthorized action this you are trying to access another agency data", []);
                        return (json_encode($response));
                    }
                } else {
                    $response = Controller::returnResponse(422, "Unauthorized action this action for admins", []);
                    return (json_encode($response));
                }
            } else {
                $response = Controller::returnResponse(422, "user does not have team", []);
                return (json_encode($response));
            }
        } catch (Exception $error) {
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
    private function calculatePrice($hours, $hourly_rate)
    {
        $price = (float)$hourly_rate * (float)$hours;
        $price = number_format($price, 2, ".", "");
        return ($price);
    }
    private function milestoneDownPaymentHandler($data)
    {
        try {
            $milestone = Milestone::where('id', $data->milestone_id)->select('*')->first();
            if ($milestone === null) {
                return ['update' => 0, 'msg' => 'nO milestone with this id'];
            }
            // $fv=  Final_proposal::where('id', $milestone->final_proposal_id)->select('down_payment_value')->first()->down_payment_value;
            //   $odl_fv=(float)$fv;
            if ($milestone->down_payment == 1) {

                if ($milestone->price == $data->milestone_price) {

                    return ['update' => 1];
                } else {

                    $downPaymentValueOld = (float)Final_proposal::where('id', $milestone->final_proposal_id)->select('down_payment_value')->first()->down_payment_value;

                    $downPaymentValue = ((float)$downPaymentValueOld - (float)$milestone->price) + (float)$data->milestone_price;
                    $finalDownPaymentValue = (float)number_format($downPaymentValue, 2, '.', '');
                    // $var=['old price'=>(float)($milestone->price),"new price"=>(float)($data->milestone_price),'dpvo'=>$downPaymentValueOld,'dpvn'=>$$fin];
                    // dd($var);
                    Final_proposal::where('id', $milestone->final_proposal_id)->update(['down_payment_value' => (float)$finalDownPaymentValue]);
                    return ['update' => 1];
                }
            } else {
                return ['update' => 1];
            }
        } catch (Exception $error) {
            return ['update' => 0, 'msg' => $error->getMessage()];
        }
    }
    private function downPaymentDelete($id)
    {
        try {
            $milestone = Milestone::where('id', $id)->select('*')->first();
            if ($milestone->down_payment == 1) {
                $downPaymentValue = (float) Final_proposal::where('id', $milestone->final_proposal_id)->select('down_payment_value')->first()->down_payment_value;

                $downPaymentValue = (float) $downPaymentValue - (float) $milestone->price;

                $finalDownPaymentValue = (float)number_format($downPaymentValue, 2, '.', '');

                Final_proposal::where('id', $milestone->final_proposal_id)->update(['down_payment_value' => $finalDownPaymentValue]);
                return ['delete' => 1];
            } else {
                return ['delete' => 1];
            }
        } catch (Exception $error) {
            return ['delete' => 0, 'msg' => $error->getMessage()];
        }
    }
    function SubmitFinal($data, $final_proposal_id, $project_id, $hourly_rate)
    {
        try {
            $rules = array(

                "milestone_name" => "required",
                "milestone_num_hours" => "required",
                "milestone_price" => "required",
                "deliverables" => "required",
                "description" => "required",
            );
            $validators = Validator::make($data, $rules);
            if ($validators->fails()) {
                return ['code' => 200, 'msg' => $validators->errors()];
            } else {
                Milestone::where('final_proposal_id', $final_proposal_id)->delete();
                foreach ($data as $milestone) {
                    if (count($milestone['deliverables']) >= 0) {
                        $deliverables = serialize($milestone['deliverables']);
                    }
                    $price = $this->calculatePrice($milestone['milestone_num_hours'], $hourly_rate);
                    $req['milestone_price'] = $price;
                    $data = array(
                        "project_id" => $project_id,
                        "final_proposal_id" => $final_proposal_id,
                        "hours" => $milestone['milestone_num_hours'],
                        "price" => $price,
                        "name" => $milestone['milestone_name'],
                        "description" => $milestone['milestone_description'],
                        "deliverables" => serialize($milestone['deliverables']),
                        "is_valid" => 1

                    );

                    $milestone = Milestone::create($data);
                }
                return ['code' => 200, 'msg' => 'successful'];
            }
        } catch (Exception $error) {
            return ['code' => 500, 'msg' => $error->getMessage()];
        }
    }
    function updateMilestonesPrices($hours, $hourly_rate, $final_proposal_id)
    {
        try {
            $FPOBJ = new Final_proposals;
            $milestones = Milestone::where('final_proposal_id', $final_proposal_id)->select('id', 'hours')->get();
            $FPOBJ->updateHoursPrice($hours, $hourly_rate, $final_proposal_id);
            foreach ($milestones as $m) {
                $hours = (int)$m->hours;
                $price = $this->calculatePrice($hours, $hourly_rate);
                Milestone::where('id', $m->id)->update(['price' => $price]);
            }
            return ['code' => 200, 'msg' => 'successfully'];
        } catch (Exception $error) {
            return ['code' => 500, 'msg' => $error->getMessage()];
        }
    }
    function updateMilestonesMonthly($hours, $hourly_rate, $final_proposal_id)

    {
        try {
            $FPOBJ = new Final_proposals;
            $milestones = Milestone::where('final_proposal_id', $final_proposal_id)->select('id', 'hours')->get();
            $FPOBJ->updateHoursPrice($hours, $hourly_rate, $final_proposal_id);
            $price = $this->calculatePrice($hours, $hourly_rate);
            Milestone::where('final_proposal_id', $final_proposal_id)->update(['price' => $price, 'hours' => $hours]);

            return ['code' => 200, 'msg' => 'successfully'];
        } catch (Exception $error) {
            return ['code' => 500, 'msg' => $error->getMessage()];
        }
    }
    function checkIsValid($milestones)
    {
        if (!in_array("", $milestones)) {
            return 1;
        } else {
            return 0;
        }
    }
    function acceptSubmission(Request $req)
    {
        $userData = Controller::checkUser($req);
        if ($userData['exist'] == 1) {
            if ($userData['group_id'] == $req->company_id) {
                if ($userData['privileges'] == 1) {
                    Milestone::where('id', $req->milestone_id)->update(['status' => 3]);
                    milestone_submission::where('id', $req->submission_id)->update(['client_comments' => $req->comments, ['status' => 3]]);
                    $response = Controller::returnResponse(200, "proposal rejected", []);
                    return (json_encode($response));
                }
                $response = Controller::returnResponse(422, "Unauthorized action this action for admins", []);
                return (json_encode($response));
            } else {
                $response = Controller::returnResponse(422, "Unauthorized you are trying to access another company data", []);
                return (json_encode($response));
            }
        } else {
            $response = Controller::returnResponse(422, "this user does not have team", []);
            return (json_encode($response));
        }
    }
    function reviseSubmission(Request $req)
    {
        $userData = Controller::checkUser($req);
        if ($userData['exist'] == 1) {
            if ($userData['group_id'] == $req->company_id) {
                if ($userData['privileges'] == 1) {
                    Milestone::where('id', $req->milestone_id)->update(['status' => 2]);
                    milestone_submission::where('id', $req->submission_id)->update(['client_comments' => $req->comments, 'status' => 2]);
                    $response = Controller::returnResponse(200, "proposal rejected", []);
                    return (json_encode($response));
                }
                $response = Controller::returnResponse(422, "Unauthorized action this action for admins", []);
                return (json_encode($response));
            } else {
                $response = Controller::returnResponse(422, "Unauthorized you are trying to access another company data", []);
                return (json_encode($response));
            }
        } else {
            $response = Controller::returnResponse(422, "this user does not have team", []);
            return (json_encode($response));
        }
    }

    function getMilestoneById(Request $req)
    {
        $userData = Controller::checkUser($req);
        if ($userData['exist'] == 1) {
            if ($userData['group_id'] == $req->group_id) {
                if ($userData['privileges'] == 1) {
                    $milestone = Milestone::where('id', $req->milestone_id)->select('*')->first();
                    $del = unserialize($milestone->deliverables);
                    $milestone->deliverables = $del;
                    $submissions = milestone_submission::where('milestone_id', $req->milestone_id)->select('*')->get();
                    $submissions_details = [];
                    foreach ($submissions as $sub) {
                        array_push($submissions_details, array(
                            "submission_id"=>$sub->id,
                            "file" => $sub->file,
                            "links" => unserialize($sub->links),
                            "agency_comments" => $sub->agency_comments,
                            "client_comments" => $sub->client_comments,
                            "submission_status" => $sub->status,
                            "submission_date" => $sub->created_at,
                        ));
                    }
                    $milestone->submissions =  $submissions_details;
                    $response = Controller::returnResponse(200, "successful", $milestone);
                    return (json_encode($response));
                }
                $response = Controller::returnResponse(422, "Unauthorized action this action for admins", []);
                return (json_encode($response));
            } else {
                $response = Controller::returnResponse(422, "Unauthorized you are trying to access another company data", []);
                return (json_encode($response));
            }
        } else {
            $response = Controller::returnResponse(422, "this user does not have team", []);
            return (json_encode($response));
        }
    }
    function addSubmissionLinks(Request $req)
    {
        try {
            if (isset($req->links)) {
                $links = serialize($req->links);
            } else {
                $links = serialize(array());
            }
            $submission = milestone_submission::where('id', $req->submission_id)->update(['links' => $links]);
            $response = Controller::returnResponse(200, "links add successfully", ["submission_id" => $req->submission_id]);
            return (json_encode($response));
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "Something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }

    function downloadSubmissionFile(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            if ($userData['exist'] == 1) {
                if ($userData['group_id'] == $req->group_id) {
                    if ($userData['privileges'] == 1) {
                     $submission= milestone_submission::where('id',$req->submission_id)->select('file','milestone_id')->get()->first();
                     $milestone=Milestone::where('id',$submission->milestone_id)->select('project_id','name')->get();
                     $dest_path='/submissions/'.$milestone->project_id;
                     $file= asset($dest_path .$submission->file);
                        if (file_exists($file)) {
                            return response()->download($file);
                        //  'Photos.zip', array('Content-Type: application/octet-stream','Content-Length: '11.
                        //   filesize($fileurl)))->deleteFileAfterSend(true);
                        } else {
                            return ['status'=>' file does not exist'];
                        }
                    } else {
                        $response = Controller::returnResponse(422, "Unauthorized action this action for admins", []);
                        return (json_encode($response));
                    }
                } else {
                    $response = Controller::returnResponse(422, "Unauthorized you are trying to access another company data", []);
                    return (json_encode($response));
                }
            } else {
                $response = Controller::returnResponse(422, "this user does not have team", []);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "Something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }

    function payMilestoneDetails(Request $request, $id)
    {
        try {
            $walletObj = new WalletsController;

            $userData = $this->checkUser($request);
            $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 2;
            if (!$condtion) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            $milstoneInfo = Milestone::where('id', '=', $id)->get()->first();
            //check if milestone exists and validate user
            if (!$milstoneInfo) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            $projectId = $milstoneInfo->project_id;
            $projectInfo = Project::where('id', '=', $projectId)->where('company_id', '=', $userData['group_id'])->get()->first();
            if (!$projectInfo) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            $wallet = $walletObj->getOrCreateWallet($userData['group_id'], 1);
            $responseData = array(
                'milestone' => $milstoneInfo,
                'wallet' => $wallet
            );
            $response = Controller::returnResponse(200, "data found", $responseData);
            return (json_encode($response));
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "Something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
}
