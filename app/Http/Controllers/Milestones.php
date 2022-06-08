<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AdminTool\WalletsTransactionsController as AdminToolWalletsTransactionsController;
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
use Response;
use App\Mail\AcceptMilestone;
use App\Mail\ReviseMilestone;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ProjectController;
use App\Models\payments;
use App\Http\Controllers\WalletsTransactionsController;
use App\Models\Group;
use App\Models\wallets_transaction;
use Barryvdh\DomPDF\Facade\Pdf;


class Milestones extends Controller
{
    function Insert(Request $req)
    {

        try {

            $finalProposalObj = new Final_proposals;
            $rules = array(
                "team_id" => "required|exists:groups,id",
                "project_id" => "required|exists:projects,id",
                "milestone_num_hours" => "required|numeric",
                "milestone_hourly_rate" => "required|numeric",
                // "milestone_price" => "required",
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

                                $new_final_proposal = $finalProposalObj->createEmptyFinalProposal($req->proposal_id, $req->team_id, $req->project_id, $userData['user_id'], $req->type);
                                if ($new_final_proposal['code'] == 422 || $new_final_proposal['code'] == 500) {
                                    $response = Controller::returnResponse($new_final_proposal['code'], 'error generating final proposal', $new_final_proposal['msg']);
                                    return json_encode($response);
                                } elseif ($new_final_proposal['code'] == 200) {

                                    if (count($req->deliverables) >= 0) {
                                        $deliverables = serialize($req->deliverables);
                                    }

                                    $price = $this->calculatePrice($req->milestone_num_hours, $req->milestone_hourly_rate);
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
                                        "hourly_rate" => $req->milestone_hourly_rate,
                                        "price" => $price,
                                        "name" => $req->milestone_name,
                                        "description" => $req->milestone_description,
                                        "deliverables" => serialize($req->deliverables),
                                        "is_valid" => $isValid
                                    );
                                    if ($req->type == 2) {
                                        $create_months = $this->createMonthlyMilestones($data, $req->counter,$new_final_proposal['msg']);
                                        if ($create_months['code'] != 200) {
                                            $response = Controller::returnResponse(500, "something went wrong milestones controller", $create_months['msg']);
                                            return (json_encode($response));
                                        }
                                        $all_milestones = Milestone::where('final_proposal_id',  $new_final_proposal['msg'])->select('id', 'price', 'hours')->get();
                                        $this->calculate_final_price($all_milestones,  $new_final_proposal['msg']);
                                        $response = Controller::returnResponse(200, "months added successfully", []);
                                        return (json_encode($response));
                                    } else {
                                        $milestone = Milestone::create($data);
                                        $all_milestones = Milestone::where('final_proposal_id',  $new_final_proposal['msg'])->select('id', 'price', 'hours')->get();
                                        $this->calculate_final_price($all_milestones,  $new_final_proposal['msg']);

                                        $response = Controller::returnResponse(200, "milestone added successfully", ["milestone_id" => $milestone->id]);
                                        return (json_encode($response));
                                    }
                                }
                            } else {
                                if ($finalProposal['status'] == -1 || $finalProposal['status'] == 3) {
                                    $final_proposal_id = $finalProposal['final_proposal_id'];
                                    if (count($req->deliverables) >= 0) {
                                        $deliverables = $req->deliverables;
                                    }
                                    $price = $this->calculatePrice($req->milestone_num_hours, $req->milestone_hourly_rate);
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
                                        "hourly_rate" => $req->milestone_hourly_rate,
                                        "price" => $req->milestone_price,
                                        "name" => $req->milestone_name,
                                        "description" => $req->milestone_description,
                                        "deliverables" => serialize($req->deliverables),
                                        "is_valid" => $isValid
                                    );
                                    // if ($finalProposal['type'] == 1) {
                                    //     $MP = $this->updateMilestonesPrices($req->hours, $req->milestone_hourly_rate, $finalProposal['final_proposal_id']);
                                    //     if ($MP['code'] == 500) {
                                    //         $response = Controller::returnResponse(500, "something wrong update prices", $MP['msg']);
                                    //         return (json_encode($response));
                                    //     }
                                    // } elseif ($finalProposal['type'] == 2) {
                                    //     $MP = $this->updateMilestonesMonthly($req->hours, $req->milestone_hourly_rate, $finalProposal['final_proposal_id']);
                                    //     if ($MP['code'] == 500) {
                                    //         $response = Controller::returnResponse(500, "something wrong update prices", $MP['msg']);
                                    //         return (json_encode($response));
                                    //     }
                                    // }
                                    if ($req->type == 2) {
                                        $milestone = $this->createMonthlyMilestones($data, $req->counter,$final_proposal_id);
                                        if ($milestone['code'] != 200) {
                                            $response = Controller::returnResponse(500, "something went wrong milestones controller", $milestone['msg']);
                                            return (json_encode($response));
                                        }
                                        $all_milestones = Milestone::where('final_proposal_id',  $final_proposal_id)->select('id', 'price', 'hours')->get();
                                        $this->calculate_final_price($all_milestones,  $final_proposal_id);
                                        $response = Controller::returnResponse(200, "months added successfully", []);
                                        return (json_encode($response));
                                    } else {
                                        $milestone = Milestone::create($data);
                                        $all_milestones = Milestone::where('final_proposal_id',  $final_proposal_id)->select('id', 'price', 'hours')->get();
                                        $this->calculate_final_price($all_milestones,  $final_proposal_id);


                                        $response = Controller::returnResponse(200, "milestone added successfully", ["milestone_id" => $milestone->id]);
                                        return (json_encode($response));
                                    }
                                    // $FP=Final_proposal::where('id',$finalProposal['final_proposal_id'])->update('')

                                } else {
                                    $response = Controller::returnResponse(422, "You already submit your proposal", []);
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
                            $price = $this->calculatePrice($req->milestone_num_hours, $req->milestone_hourly_rate);
                            $req['milestone_price'] = $price;

                            // $update = $this->milestoneDownPaymentHandler($req);
                            // if ($update['update'] == 1) {
                            //     if ($finalProposal['type'] == 1) {
                            //         $MP = $this->updateMilestonesPrices($req->hours, $req->milestone_hourly_rate, $finalProposal['final_proposal_id']);
                            //         if ($MP['code'] == 500) {
                            //             $response = Controller::returnResponse(500, "something wrong update prices", $MP['msg']);
                            //             return (json_encode($response));
                            //         }
                            //     } elseif ($finalProposal['type'] == 2) {
                            //         $MP = $this->updateMilestonesMonthly($req->hours, $req->milestone_hourly_rate, $finalProposal['final_proposal_id']);
                            //         if ($MP['code'] == 500) {
                            //             $response = Controller::returnResponse(500, "something wrong update prices", $MP['msg']);
                            //             return (json_encode($response));
                            //         }
                            //     }

                            $countDeliverables = "";
                            if (count($req->deliverables) > 0) {
                                $countDeliverables = "1";
                            }
                            $isValidArray = array($req->milestone_name, $req->milestone_description, $countDeliverables, $price, $req->milestone_num_hours);
                            $isValid = $this->checkIsValid($isValidArray);

                            $milestone = Milestone::where('id', $req->milestone_id)
                                ->update([
                                    'name' => $req->milestone_name, 'hours' => $req->milestone_num_hours, 'price' => $req->milestone_price, 'hourly_rate' => $req->milestone_hourly_rate,
                                    'description' => $req->milestone_description, 'deliverables' => serialize($req->deliverables), 'is_valid' => $isValid
                                ]);
                            $all_milestones = Milestone::where('final_proposal_id',  $finalProposal['final_proposal_id'])->select('id', 'price', 'hours')->get();
                            $this->calculate_final_price($all_milestones,  $finalProposal['final_proposal_id']);
                            $response = Controller::returnResponse(200, "milestone updated successful", []);
                            return (json_encode($response));
                            // } else {
                            //     $response = Controller::returnResponse(500, "something wrong down payment handler", $update['msg']);
                            //     return (json_encode($response));
                            // }
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
                            // $del = $this->downPaymentDelete($req->milestone_id);
                            // if ($del['delete'] == 1) {
                            $milestone = Milestone::where('id', $req->milestone_id)->delete();
                            $all_milestones = Milestone::where('final_proposal_id',  $finalProposal['final_proposal_id'])->select('id', 'price', 'hours')->get();
                            $this->calculate_final_price($all_milestones,  $finalProposal['final_proposal_id']);
                            $response = Controller::returnResponse(200, "milestone deleted successful", []);
                            return (json_encode($response));
                            // } else {
                            //     $response = Controller::returnResponse(500, "something wrong down payment handler", $del['msg']);
                            //     return (json_encode($response));
                            // }
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
                            "milestone_hourly_rate" => $milestone->hourly_rate,
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
                    "milestone_hourly_rate" => $milestone->hourly_rate,
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
                            $project_id = $req->project_id;
                            $submission = milestone_submission::create($req->except(['submission_file']));
                            $submission_id = $submission->id;
                            $milestone = Milestone::where('id', $req->milestone_id)->select('name')->first();
                            $milestoneName = str_replace(' ', '-', $milestone->name);
                            $originalName = str_replace(' ', '-',  $req->file('submission_file')->getClientOriginalName());
                            $originalName = str_replace('_', '-',  $originalName);
                            $submissionName = time() . '_' . $milestoneName . '_' . $originalName;
                            $submission_file = $req->submission_file;
                            // $dist='/submissions/'.$project_id;
                            $dist = public_path() . '/submissions/' . $req->project_id;
                            if (!File::exists($dist)) {
                                // if (!file_exists($dist)) {
                                // if (!file_exists($dist)) {
                                File::makeDirectory(public_path() . '/submissions/' . $project_id, 0755, true);
                                $submission_file->move(public_path() . '/submissions/' . $project_id, $submissionName);
                                $this->updateSubmissionFile($submission_id, $submissionName);
                                $this->updateStatus($req->milestone_id, '1');
                            } else {
                                $submission_file->move(public_path() . '/submissions/' . $project_id, $submissionName);
                                $this->updateSubmissionFile($submission_id, $submissionName);
                                $this->updateStatus($req->milestone_id, '1');
                            }
                            $projectObj=new ProjectController;
                            $company_id = $projectObj->getCompanyInfoByProjectId( $req->project_id)->id;
                            // Controller::sendNotification($company_id,"Proposals",'Milestone Submitted');
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
    private function calculatePrice($hours, $milestone_hourly_rate)
    {
        $price = (float)$milestone_hourly_rate * (float)$hours;
        $price = number_format($price, 2, ".", "");
        return ($price);
    }
    // private function milestoneDownPaymentHandler($data)
    // {
    //     try {
    //         $milestone = Milestone::where('id', $data->milestone_id)->select('*')->first();
    //         if ($milestone === null) {
    //             return ['update' => 0, 'msg' => 'nO milestone with this id'];
    //         }
    //         // $fv=  Final_proposal::where('id', $milestone->final_proposal_id)->select('down_payment_value')->first()->down_payment_value;
    //         //   $odl_fv=(float)$fv;
    //         if ($milestone->down_payment == 1) {

    //             if ($milestone->price == $data->milestone_price) {

    //                 return ['update' => 1];
    //             } else {

    //                 $downPaymentValueOld = (float)Final_proposal::where('id', $milestone->final_proposal_id)->select('down_payment_value')->first()->down_payment_value;

    //                 $downPaymentValue = ((float)$downPaymentValueOld - (float)$milestone->price) + (float)$data->milestone_price;
    //                 $finalDownPaymentValue = (float)number_format($downPaymentValue, 2, '.', '');
    //                 // $var=['old price'=>(float)($milestone->price),"new price"=>(float)($data->milestone_price),'dpvo'=>$downPaymentValueOld,'dpvn'=>$$fin];
    //                 // dd($var);
    //                 Final_proposal::where('id', $milestone->final_proposal_id)->update(['down_payment_value' => (float)$finalDownPaymentValue]);
    //                 return ['update' => 1];
    //             }
    //         } else {
    //             return ['update' => 1];
    //         }
    //     } catch (Exception $error) {
    //         return ['update' => 0, 'msg' => $error->getMessage()];
    //     }
    // }
    // private function downPaymentDelete($id)
    // {
    //     try {
    //         $milestone = Milestone::where('id', $id)->select('*')->first();
    //         if ($milestone->down_payment == 1) {
    //             $downPaymentValue = (float) Final_proposal::where('id', $milestone->final_proposal_id)->select('down_payment_value')->first()->down_payment_value;

    //             $downPaymentValue = (float) $downPaymentValue - (float) $milestone->price;

    //             $finalDownPaymentValue = (float)number_format($downPaymentValue, 2, '.', '');

    //             Final_proposal::where('id', $milestone->final_proposal_id)->update(['down_payment_value' => $finalDownPaymentValue]);
    //             return ['delete' => 1];
    //         } else {
    //             return ['delete' => 1];
    //         }
    //     } catch (Exception $error) {
    //         return ['delete' => 0, 'msg' => $error->getMessage()];
    //     }
    // }
    function SubmitFinal($data, $final_proposal_id, $project_id)
    {
        try {
            $rules = array(

                "milestone_name" => "required",
                "milestone_num_hours" => "required",
                "milestone_hourly_rate" => "required",
                // "milestone_price" => "required",
                "deliverables" => "required",
                "milestone_description" => "required",
            );

            // Milestone::where('final_proposal_id', $final_proposal_id)->delete();
            foreach ($data as $milestone) {
                $validators = Validator::make($milestone, $rules);
                if ($validators->fails()) {
                    return ['code' => 422, 'msg' => $validators->errors()];
                }
                if (count($milestone['deliverables']) >= 0) {
                    $deliverables = serialize($milestone['deliverables']);
                }
                $price = $this->calculatePrice($milestone['milestone_num_hours'], $milestone['milestone_hourly_rate']);
                $req['milestone_price'] = $price;
                Milestone::where('id', $milestone['milestone_id'])->update([
                    "project_id" => $project_id, "final_proposal_id" => $final_proposal_id,
                    "hours" => $milestone['milestone_num_hours'], "price" => $price, "name" => $milestone['milestone_name'], "description" => $milestone['milestone_description'],
                    "deliverables" => serialize($milestone['deliverables']),
                    "is_valid" => 1
                ]);
                // $data = array(
                //     "project_id" => $project_id,
                //     "final_proposal_id" => $final_proposal_id,
                //     "hours" => $milestone['milestone_num_hours'],
                //     "price" => $price,
                //     "name" => $milestone['milestone_name'],
                //     "description" => $milestone['milestone_description'],
                //     "deliverables" => serialize($milestone['deliverables']),
                //     "is_valid" => 1

                // );

                // $milestone = Milestone::create($data);
            }
            return ['code' => 200, 'msg' => 'successful'];
        } catch (Exception $error) {
            return ['code' => 500, 'msg' => $error->getMessage()];
        }
    }
    // function updateMilestonesPrices($hours, $milestone_hourly_rate, $final_proposal_id)
    // {
    //     try {
    //         $FPOBJ = new Final_proposals;
    //         $milestones = Milestone::where('final_proposal_id', $final_proposal_id)->select('id', 'hours')->get();
    //         $FPOBJ->updateHoursPrice($hours, $milestone_hourly_rate, $final_proposal_id);
    //         foreach ($milestones as $m) {
    //             $hours = (int)$m->hours;
    //             $price = $this->calculatePrice($hours, $milestone_hourly_rate);
    //             Milestone::where('id', $m->id)->update(['price' => $price]);
    //         }
    //         return ['code' => 200, 'msg' => 'successfully'];
    //     } catch (Exception $error) {
    //         return ['code' => 500, 'msg' => $error->getMessage()];
    //     }
    // }
    // function updateMilestonesMonthly($hours, $hourly_rate, $final_proposal_id)

    // {
    //     try {
    //         $FPOBJ = new Final_proposals;
    //         $milestones = Milestone::where('final_proposal_id', $final_proposal_id)->select('id', 'hours')->get();
    //         $FPOBJ->updateHoursPrice($hours, $hourly_rate, $final_proposal_id);
    //         $price = $this->calculatePrice($hours, $hourly_rate);
    //         Milestone::where('final_proposal_id', $final_proposal_id)->update(['price' => $price, 'hours' => $hours]);

    //         return ['code' => 200, 'msg' => 'successfully'];
    //     } catch (Exception $error) {
    //         return ['code' => 500, 'msg' => $error->getMessage()];
    //     }
    // }
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
        try {
            $userData = Controller::checkUser($req);
            if ($userData['exist'] == 1) {
                if ($userData['group_id'] == $req->company_id) {
                    if ($userData['privileges'] == 1) {
                        milestone_submission::where('id', $req->submission_id)->update(['client_comments' => $req->comments, 'status' => 3]);
                        Milestone::where('id', $req->milestone_id)->update(['status' => 3]);
                        $walletObj = new WalletsTransactionsController;
                      $trans=  $walletObj -> makePaymentTransactionDepositAgency($req->milestone_id);
                      if($trans['responseCode'] !=200)
                      {
                        $response = Controller::returnResponse(500, "something went wrong", $trans['paymentMsg']);
                        return (json_encode($response));
                      }
                        $active= $this->ActivateNext($req->milestone_id);
                        if ($active['code' != 200])
                        {
                            $response = Controller::returnResponse(500, "something went wrong", $active['msg']);
                            return (json_encode($response));
                        }
                        // $this->PayIfDownPayment($req->milestone_id);
                        $milestoneDetails = Milestone::where('id', $req->milestone_id)->select('name', 'final_proposal_id')->first();
                        $agency = DB::table('groups')
                            ->Join('final_proposals', 'groups.id', '=', 'final_proposals.team_id')
                            ->select('groups.name', 'groups.id', 'final_proposals.project_id')
                            ->where('final_proposals.id', '=', $milestoneDetails->final_proposal_id)
                            ->first();
                        $groupMemsObj = new GroupMembersController;
                        $projectObj = new ProjectController;
                        $agencyAdmin = $groupMemsObj->getTeamAdminByGroupId($agency->id);
                        $projectInfo = json_decode($projectObj->getProject($agency->project_id))->data;
                        $adminName = $agencyAdmin->first_name . $agencyAdmin->last_name;
                        $details = [
                            "subject" => 'Your Submission Accepted',
                            "name" => $adminName,
                            "project_id" =>  $projectInfo->id,
                            "project_name" =>  $projectInfo->name,
                            "milestone" => ['name' => $milestoneDetails->name, 'client_comments' => $req->comments]
                        ];
                        Mail::mailer('smtp2')->to($agencyAdmin->email)->send(new AcceptMilestone($details));
                        $response = Controller::returnResponse(200, "proposal rejected", []);
                        return (json_encode($response));
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
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function reviseSubmission(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            if ($userData['exist'] == 1) {
                if ($userData['group_id'] == $req->company_id) {
                    if ($userData['privileges'] == 1) {

                        milestone_submission::where('id', $req->submission_id)->update(["client_comments" => $req->comments, "status" => 2]);
                        Milestone::where('id', $req->milestone_id)->update(['status' => 2]);
                        $milestoneDetails = Milestone::where('id', $req->milestone_id)->select('name', 'final_proposal_id')->first();
                        $agency = DB::table('groups')
                            ->Join('final_proposals', 'groups.id', '=', 'final_proposals.team_id')
                            ->select('groups.name', 'groups.id', 'final_proposals.project_id')
                            ->where('final_proposals.id', '=', $milestoneDetails->final_proposal_id)
                            ->first();
                        $groupMemsObj = new GroupMembersController;
                        $projectObj = new ProjectController;
                        $agencyAdmin = $groupMemsObj->getTeamAdminByGroupId($agency->id);
                        $projectInfo = json_decode($projectObj->getProject($agency->project_id))->data;
                        $adminName = $agencyAdmin->first_name . $agencyAdmin->last_name;
                        $details = [
                            "subject" => 'Review Your Submission',
                            "name" => $adminName,
                            "project_id" =>  $projectInfo->id,
                            "project_name" =>  $projectInfo->name,
                            "milestone" => ['name' => $milestoneDetails->name, 'client_comments' => $req->comments]
                        ];
                        Mail::mailer('smtp2')->to($agencyAdmin->email)->send(new ReviseMilestone($details));
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
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
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
                            "submission_id" => $sub->id,
                            "file" => $sub->file,
                            "links" => unserialize($sub->links),
                            "agency_comments" => $sub->agency_comments,
                            "client_comments" => $sub->client_comments,
                            "submission_status" => $sub->status,
                            "submission_date" => $sub->created_at,
                        ));
                    }
                    $can = $this->canSubmit($req->milestone_id);
                    $milestone->submissions =  $submissions_details;
                    $milestone->can_submit =  $can;

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
                        $submission = milestone_submission::where('id', $req->submission_id)->select('file', 'milestone_id')->get()->first();
                        $milestone = Milestone::where('id', $submission->milestone_id)->select('project_id', 'name')->get()->first();
                        $dest_path = "/submissions/" . $milestone->project_id . "/" . $submission->file;
                        $file = asset($dest_path);
                        $filecheck = public_path('submissions/' . $milestone->project_id . "/" . $submission->file);

                        if (file_exists($filecheck)) {
                            $file = asset('submissions/' . $milestone->project_id . "/" . $submission->file);
                            // return Response()->download($file);
                            $response = Controller::returnResponse(200, "successful", ['link' => $file]);
                            return (json_encode($response));
                            //  'Photos.zip', array('Content-Type: application/octet-stream','Content-Length: '11.
                            //   filesize($fileurl)))->deleteFileAfterSend(true);
                        } else {
                            $response = Controller::returnResponse(422, "file does not exist", []);
                            return (json_encode($response));
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
    /**
     * $id Milestone Id 
     */
    function canSubmit($id)
    {
        $can = 0;
        $status1 = Milestone::where('id', $id)->select('status')->first()->status;
        if ($status1 == 3 || $status1 == 1) {
            $can = 0;
        } else {
            $final_proposal_id = Milestone::where('id', $id)->select('final_proposal_id')->first()->final_proposal_id;
            $milestones = Milestone::where('final_proposal_id', $final_proposal_id)->select('id', 'status')->get();
            $ids = $milestones->pluck('id')->toArray();
            $index = array_search($id, $ids); //index of the current milestone
            if ($index == 0) {
                $status = Milestone::where('id', $id)->select('status')->first()->status;
                if ($status == 0 || $status == 2) {
                    $can = 1;
                }
            } else {
                $check_index = $index - 1;
                $status2 = Milestone::where('id', $ids[$check_index])->select('status')->first()->status;
                if ($status2 == 3) {
                    $can = 1;
                }
            }
        }
        return $can;
    }

    /**
     * check if the milestone includes in the down payment if yes pay it
     */
    // function PayIfDownPayment($id)
    // {
    //     $down_payment = Milestone::where('id', $id)->select('down_payment')->first()->down_payment;
    //     $walletTransactionsObj = new AdminToolWalletsTransactionsController;
    //     if ($down_payment == 1) {
    //         $payment = payments::where('milestone_id', $id)->select('*')->first();
    //         $walletTransactionsObj->makePaymentTransactionDeposit($payment);
    //         return 1;
    //     } else {
    //         return 0;
    //     }
    // }
    function printMilestoneInvoice(Request $req)
    {
        try {
            $id = $req->id;
            $walletObj = new WalletsController;
            $userData = $this->checkUser($req);
            $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1;
            $wallet = $walletObj->getOrCreateWallet($userData['group_id'], 1);
            if (!$condtion) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            if ($userData['type'] == 1) {
                $payment = payments::where('milestone_id', '=', $id)->where('agency_id', '=', $userData['group_id'])->where('status', '<>', 0)
                    ->get()->first();
            } else {
                $payment = payments::where('milestone_id', '=', $id)->where('company_id', '=', $userData['group_id'])->where('status', '<>', 0)
                    ->get()->first();
            }
            if (!$payment) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            $transaction = wallets_transaction::where('payment_id', '=', $payment->id)->where('wallet_id', '=', $wallet->id)->get()->first();

            if (!$transaction) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }

            $payment = payments::where('id', '=', $transaction->payment_id)->get()->first();
            $milestone = Milestone::where('id', '=', $payment->milestone_id)->get()->first();
            $project = Project::select('name')->where('id', '=', $payment->project_id)->get()->first();
            $agency = Group::select('name')->where('id', '=', $payment->agency_id)->get()->first();
            $company = Group::select('name')->where('id', '=', $payment->company_id)->get()->first();

            $total = 0;
            if ($userData['type'] == 1) {
                $total = $payment->agency_total_price;
            } else {
                $total = $payment->total_price;
            }
            $data = array(
                'milestone_price' => $payment->milestone_price,
                'tapflow_fee' => $payment->tapflow_fee,
                'total' => $total,
                'milestone_name' => $milestone->name,
                'project_name' => $project->name,
                'agency_name' => $agency->name,
                'company_name' => $company->name,
                'id' => $transaction->id,
                'date' => date('Y-m-d', strtotime($transaction->created_at)),
                'type' => $transaction->type,
                'user_type' => $userData['type'],
            );
            $filename = "invoice-" . $transaction->id . ".pdf";
            $pdf = PDF::loadView('pdf/milestone', $data);
            return $pdf->download($filename);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function calculate_final_price($milestones, $FP_id)
    {

        $total_price = 0;
        $total_hours = 0;
        foreach ($milestones as $milestone) {
            $total_price += (float)$milestone->price;
            $total_hours += (int)$milestone->hours;
        }
        $total_price = number_format($total_price, 2, ".", "");

        Final_proposal::where('id', $FP_id)->update(['price' => $total_price, 'hours' => $total_hours]);
    }
    function createMonthlyMilestones($data, $counter,$final_proposal_id)
    {

        try {
            $c=Milestone::where('final_proposal_id',$final_proposal_id)->select('id')->count();
            $nameCounter=(int)$c+1;
            for ($i = 0; $i < $counter; $i++) {
                $name="Month ". $nameCounter;
                $data['name']=$name;
                Milestone::create($data);
                $nameCounter+=1;
            }
            return ['code' => 200];
        } catch (Exception $error) {

            return ['code' => 500, 'msg' => $error->getMessage()];
        }
    }
    private function ActivateNext($current_id)
    {
        try{
        $current_milestone = Milestone::where('id', $current_id)->select('status', 'final_proposal_id')->first();
        if ($current_milestone->status != 3) {
            return ['code'=>500,'msg'=>'current milestone not closed'];
        }
        $all_milestones = Milestone::where('final_proposal_id', $current_milestone->final_proposal_id)->select('id', 'status')->first();
        $milestones_ids = $all_milestones->pluck('id')->toArray();
        $last_index = count($milestones_ids) - 1;
        $project_id = Final_proposal::where('id', $current_milestone->final_proposal_id)->select('project_id')->first()->project_id;
        $current_index = (int)array_search($current_id, $milestones_ids);
        $next_index = $current_index + 1;
        if ($current_index == $last_index) {
            Project::where('id', $project_id)->update(['status' => 2]);
        } else {
            $status = Milestone::where('id', $next_index)->select('status')->first()->status;
            Milestone::where('id', $milestones_ids[$next_index])->update(['status' => 4]);
        }
    }
    catch(Exception $error)
    { return ['code'=>500,'msg'=>$error->getMessage()];}
    }
}
