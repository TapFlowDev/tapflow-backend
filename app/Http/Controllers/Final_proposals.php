<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Final_proposal;
use App\Models\Project;
use App\Models\tasks;
use Exception;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\Proposals;
use App\Http\Controllers\GroupMembersController;
use App\Http\Controllers\TeamController;
use App\Models\Group_member;
use App\Models\Milestone;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\Finally_;
use Stripe\Issuing\Card;
use function PHPUnit\Framework\isEmpty;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubmitFinalProposal;
use App\Mail\FinalProposalActions;
class Final_proposals extends Controller
{
    function Insert(Request $req)
    {
        $userData = Controller::checkUser($req);
        $proposalObj = new Proposals;
        $milestoneObj = new Milestones;
        if ($userData['privileges'] == 1) {
            $team_id = $userData['group_id'];
            if ($userData['group_id'] == $req->team_id) {
                $init_proposal = $proposalObj->getProposalInfo($req->project_id, $team_id);
                if ($init_proposal['exist'] == 1) {
                    if ($init_proposal['proposal']->status == 1) {
                        $req['user_id'] = $userData['user_id'];
                        $ifExist = $this->checkIfExists($req->project_id,  $team_id);
                        $rules = array(
                            "team_id" => "required|exists:groups,id",
                            "project_id" => "required|exists:projects,id",
                            "proposal_id" => "required|exists:proposals,id",
                            "hourly_rate" => "required|numeric",
                            "hours" => "required|numeric",
                            "down_payment" => "required", //value 0=>no down payment 1=>there is down payment
                            "type" => "required" //1=>regular milestones ,2=>monthly based milestones
                        );
                        $validators = Validator::make($req->all(), $rules);
                        if ($validators->fails()) {
                            $responseData = $validators->errors();
                            $response = Controller::returnResponse(101, "Validation Error", $responseData);
                            return (json_encode($response));
                        } else {
                        if ($ifExist['exist'] == '0') {
                        
                                try {
                                    $price = $this->calculatePrice($req->hours, $req->hourly_rate);
                                    $req['price'] = $price;
                                    $final_proposal = Final_proposal::create($req->except(['down_payment']) + ['status' => -1]);
                                    if ($req->down_payment['status'] == 1) {
                                        $this->downPaymentHandler($req->down_payment, $final_proposal->id);
                                    } else {
                                        Final_proposal::where('id', $final_proposal->id)->update(['down_payment' => 0, 'down_payment_value' => 0.00]);
                                        Milestone::where('final_proposal_id', $final_proposal->id)->update(['down_payment' => 0]);
                                    }
                                    $response = Controller::returnResponse(200, 'Final proposal add successfully', $final_proposal->id);
                                    return (json_encode($response));
                                } catch (Exception $error) {
                                    $response = Controller::returnResponse(500, 'something wrong', $error->getMessage());
                                    return json_encode($response);
                                }
                            
                        }
                        //if the proposal exists
                        else {
                            $proposal = json_decode($this->getProposalDetailsById($ifExist['final_proposal_id']));
                            $status = $proposal->status;
                            if ($status == -1 || $status == 3) {
                                $price = $this->calculatePrice($req->hours, $req->hourly_rate);
                                $req['price'] = $price;
                                if ($ifExist['type'] == 1) {
                                    $MP = $milestoneObj->updateMilestonesPrices($req->hours, $req->hourly_rate, $ifExist['final_proposal_id']);
                                    if ($MP['code'] == 500) {
                                        $response = Controller::returnResponse(500, "something wrong update prices", $MP['msg']);
                                        return (json_encode($response));
                                    }
                                } elseif ($ifExist['type'] == 2) {
                                    $MP = $milestoneObj->updateMilestonesMonthly($req->hours, $req->hourly_rate, $ifExist['final_proposal_id']);
                                    if ($MP['code'] == 500) {
                                        $response = Controller::returnResponse(500, "something wrong update prices", $MP['msg']);
                                        return (json_encode($response));
                                    }
                                }
                                $update_final = $this->updateQuery($ifExist['final_proposal_id'], $req);
                                if ($req->down_payment['status'] == 1) {
                                    $this->downPaymentHandler($req->down_payment, $ifExist['final_proposal_id']);
                                } else {
                                    Final_proposal::where('id', $ifExist['final_proposal_id'])->update(['down_payment' => 0, 'down_payment_value' => 0.00]);
                                    Milestone::where('final_proposal_id', $ifExist['final_proposal_id'])->update(['down_payment' => 0]);
                                }
                                $response = Controller::returnResponse(200, 'update data successful', []);
                                return json_encode($response);
                            } else {
                                $responseData = $this->selectQuery($ifExist['final_proposal_id']);
                                $response = Controller::returnResponse(200, 'You already have proposal', $responseData);
                                return json_encode($response);
                            }
                        }
                    }
                    } else {
                        $response = Controller::returnResponse(422, 'your initial proposal status not accepted ', []);
                        return json_encode($response);
                    }
                } else {
                    $response = Controller::returnResponse(422, 'you do not have initial proposal ', []);
                    return json_encode($response);
                }
            } else {
                $response = Controller::returnResponse(422, 'Unauthorized you are trying to access another agency proposal ', []);
                return json_encode($response);
            }
        } else {
            $response = Controller::returnResponse(422, 'Unauthorized action this action for admins only or you do not have team', []);
            return json_encode($response);
        }
    }
    function checkIfExists($project_id, $team_id)
    {
        $final_proposal = DB::table('final_proposals')
            ->select('id', 'type', 'status')
            ->where('team_id', '=', $team_id)
            ->where('project_id', '=', $project_id)
            ->first();
        if ($final_proposal == null) {
            return ['exist' => 0];
        } else {
            return ['exist' => 1, "final_proposal_id" => $final_proposal->id, 'type' => (int)$final_proposal->type, 'status' => $final_proposal->status];
        }
    }
    //this query used to update final proposal data and keep proposal id the same
    private function updateQuery($id, $data)
    {
        $update = DB::table('final_proposals')
            ->where('id', $id)
            ->update([
                'title' => $data->title, 'price' => $data->price,
                'starting_date' => $data->starting_date,
                'description' => $data->description,
                'hours' => $data->hours, 'hourly_rate' => $data->hourly_rate,
                'user_id' => $data->user_id,
                'type' => $data->type,
            ]);
        return $update;
    }
    private function selectQuery($id)
    {
        $final_proposal = Final_proposal::where('id', $id)
            ->first()
            ->makeHidden(['created_at', 'updated_at']);
        return $final_proposal;
    }
    function getProposalDetailsById($id)
    {
        $milestone = new Milestones;
        $final_proposal = $this->selectQuery($id);
        // $milestones = $milestone->getMilestoneByProposalId($id);
        // $final_proposal->milestones = $milestones;
        return json_encode($final_proposal);
    }
    function getProposalById($id)
    {
        return $this->selectQuery($id);
    }
    function getProjectProposalsById(Request $req, $project_id, $offset, $limit)
    {
        $userData = $req->user();
        $project = Project::where('id', $project_id)->select('user_id')->first();
        $GroupControllerObj = new GroupController;
        $project_group_id = $GroupControllerObj->getGroupIdByUserId($project->user_id);
        $user_group_id = $GroupControllerObj->getGroupIdByUserId($userData->id);
        if ($user_group_id == $project_group_id) {

            $page = ($offset - 1) * $limit;
            try {
                $proposals = DB::table('final_proposals')
                    ->select('final_proposals.*')
                    ->where('project_id', $project_id)
                    ->where('status', '!=', -1)
                    ->distinct()
                    ->latest()->offset($page)->limit($limit)
                    ->get();
                $milestone = new Milestones;
                foreach ($proposals as $proposal) {
                    $proposal->agency_info =  $GroupControllerObj->getGroupNameAndImage($proposal->team_id);
                    $milestones_array = $milestone->getMilestoneByProposalId($proposal->id);
                    $m_ids = array();
                    array_push($m_ids, $milestones_array[0]['milestone_id']);
                    $proposal->milestones = $milestones_array;
                    foreach ($m_ids as $mid) {
                        $all_people = Db::table('tasks')
                            ->join('assigned_tasks', 'tasks.id', '=', 'assigned_tasks.task_id')
                            ->join('users', 'assigned_tasks.user_id', '=', 'users.id')
                            ->join('freelancers', 'users.id', '=', 'freelancers.user_id')
                            ->where('tasks.milestone_id', '=', $mid)
                            ->select('users.id', 'users.first_name', 'users.last_name', 'freelancers.image')
                            ->get();
                    }
                    $all_people = array($all_people);
                    $all_people = array_unique($all_people);
                    $proposal->all_people = $all_people;
                }
                $response = Controller::returnResponse(200, "successful", $proposals);
                return (json_encode($response));
            } catch (Exception $error) {
                $response = Controller::returnResponse(500, "something wrong", $error->getMessage());
                return (json_encode($response));
            }
        } else {
            $response = Controller::returnResponse(422, "You are trying to get another company data", []);
            return (json_encode($response));
        }
    }
    function getProposalDetailsByProject_id($project_id)
    {

        $milestone = new Milestones;
        $final_proposal = Final_proposal::where('project_id', $project_id)
            ->select(
                'id',
                'title',
                'user_id',
                'proposal_id',
                'project_id',
                'team_id',
                'hours',
                'hourly_rate',
                'price',
                'down_payment',
                'down_payment_value',
                'starting_date',
                'type',
                'status',
                'created_at'
            )->first();

        $milestones = $milestone->getMilestoneByProposalId($final_proposal->id);
        $final_proposal->milestones = $milestones;
        return ($final_proposal);
    }
    function createEmptyFinalProposal($hourly_rate, $hours, $proposal_id, $team_id, $project_id, $user_id, $type)
    {
        try {
            $proposalObj = new Proposals;
            $init_proposal = $proposalObj->getProposalInfo($project_id, $team_id);
            if ($init_proposal['exist'] == 1) {
                if ($init_proposal['proposal']->status == 1) {
                    $final_proposal = Final_proposal::create(['hourly_rate' => $hourly_rate, 'hours' => $hours, 'proposal_id' => $proposal_id, 'status' => -1, 'team_id' => $team_id, 'project_id' => $project_id, 'user_id' => $user_id, 'type' => $type]);
                    return ['code' => 200, 'msg' => $final_proposal->id];
                } else {

                    return ['code' => 422, 'msg' => 'your initial proposal status not accepted'];
                }
            } else {

                return ['code' => 422, 'msg' => 'you do not have initial proposal'];
            }
        } catch (Exception $error) {
            return ['code' => 500, 'msg' => $error->getMessage()];
        }
    }
    function getFinalProposalByProjectIdAndTeamId(Request $req, $project_id, $team_id)
    {
        try {
            $userData = Controller::checkUser($req);
            $proposalObj = new Proposals;
            if ($userData['privileges'] == 1) {
                $team_id = $userData['group_id'];
                if ($userData['group_id'] == $team_id) {
                    $init_proposal = $proposalObj->getProposalInfo($project_id, $team_id);
                    if ($init_proposal['exist'] == 1) {
                        if ($init_proposal['proposal']->status == 1) {

                            $final_proposal = DB::table('final_proposals')
                                ->where('project_id', '=', $project_id)
                                ->where('team_id', '=', $team_id)
                                ->first();
                            $final_proposal->hours = $final_proposal->hours;
                            $response = Controller::returnResponse(200, 'successful ', $final_proposal);
                            return json_encode($response);
                        } else {
                            $response = Controller::returnResponse(422, 'your initial proposal status not accepted ', []);
                            return json_encode($response);
                        }
                    } else {
                        $response = Controller::returnResponse(422, 'you do not have initial proposal ', []);
                        return json_encode($response);
                    }
                } else {
                    $response = Controller::returnResponse(422, 'Unauthorized you are trying to access another agency proposal ', []);
                    return json_encode($response);
                }
            } else {
                $response = Controller::returnResponse(422, 'Unauthorized action this action for admins only or you do not have team', []);
                return json_encode($response);
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'something went wrong', $error->getMessage());
            return json_encode($response);
        }
    }
    private function downPaymentHandler($data, $proposal_id)
    {
        try {
            if ($data['value'] > 0) {
                $sum = 0;
                Milestone::where('final_proposal_id', $proposal_id)->update(['down_payment' => 0]);
                foreach ($data['details'] as $milestone) {
                    $sum += $milestone['milestone_price'];
                    $milestone_id = $milestone['milestone_id'];
                    Milestone::where('id', $milestone_id)->update(['down_payment' => 1]);
                }
                $sum = number_format($sum, 2, '.', '');

                Final_proposal::where('id', $proposal_id)->update(['down_payment' => 1, 'down_payment_value' => $sum]);
            }
        } catch (Exception $error) {
        }
    }
    private function calculatePrice($hours, $hourly_rate)
    {
        $price = (float)$hourly_rate * (float)$hours;

        $price = number_format($price, 2, '.', '');

        return $price;
    }
    function getFullFinalProposalById(Request $req, $id)
    {
        try {
            $userData = Controller::checkUser($req);
            if ($userData['privileges'] == 1) {

                $milestone = new Milestones;
                $team = new TeamController;
                $projectObj = new ProjectController;
                $final_proposal = Final_proposal::where('id', $id)
                    ->where('status', '!=', -1)


                    ->select(
                        'id',
                        'title',
                        'user_id',
                        'proposal_id',
                        'project_id',
                        'team_id',
                        'hours',
                        'hourly_rate',
                        'price',
                        'down_payment',
                        'down_payment_value',
                        'description',
                        'starting_date',
                        'type',
                        'status',
                        'created_at'
                    )->first();
                $company_id = $projectObj->getProjectCompanyId($final_proposal->project_id);
                if ($company_id == $userData['group_id']) {
                    $milestones = $milestone->getMilestoneByProposalId($final_proposal->id);
                    $agency = $team->get_team_info($final_proposal->team_id);
                    $final_proposal->milestones = $milestones;

                    if ($agency->image == null) {
                        $image = asset('images/profile-pic.jpg');
                        $agency->image = $image;
                    } else {
                        $image = asset('images/companies/' . $agency->image);

                        $agency->image = $image;
                    }

                    $final_proposal->agency_info = $agency;
                    $response = Controller::returnResponse(200, 'successful', $final_proposal);
                    return json_encode($response);
                } else {
                    $response = Controller::returnResponse(422, 'Unauthorized action you are trying to get another company data', []);
                    return json_encode($response);
                }
            } else {
                $response = Controller::returnResponse(422, 'Unauthorized action this action for admins only or you do not have team', []);
                return json_encode($response);
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'something went wrong', $error->getMessage());
            return json_encode($response);
        }
    }
    function submitFinalProposal(Request $req)
    {
        
        $userData = Controller::checkUser($req);
        $proposalObj = new Proposals;
        $final_proposal_status = 0;
        if ($userData['privileges'] == 1) {
            $team_id = $userData['group_id'];
            if ($userData['group_id'] == $req->team_id) {
                $init_proposal = $proposalObj->getProposalInfo($req->project_id, $team_id);
                if ($init_proposal['exist'] == 1) {
                    if ($init_proposal['proposal']->status == 1) {
                        $req['user_id'] = $userData['user_id'];
                        $ifExist = $this->checkIfExists($req->project_id,  $team_id);
                        if ($ifExist['exist'] == '0') {
                            $rules = array(
                                "title" => "required",
                                "team_id" => "required|exists:groups,id",
                                "project_id" => "required|exists:projects,id",
                                "proposal_id" => "required|exists:proposals,id",
                                "hourly_rate" => "required|numeric",
                                "starting_date" => "required|date",
                                "down_payment" => "required", //value 0=>no down payment 1=>there is down payment
                                "type" => "required", //1=>regular milestones ,2=>monthly based milestones
                                "milestones" => "required"
                            );
                            $validators = Validator::make($req->all(), $rules);
                            if ($validators->fails()) {
                                $responseData = $validators->errors();
                                $response = Controller::returnResponse(101, "Validation Error", $responseData);
                                return (json_encode($response));
                            } else {
                                try {
                                    $price = $this->calculatePrice($req->hours, $req->hourly_rate);
                                    $req['price'] = $price;
                                    $final_proposal = Final_proposal::create($req->except(['down_payment']) + ['status' => $final_proposal_status]);
                                    if ($req->down_payment['status'] == 1) {
                                        $this->downPaymentHandler($req->down_payment, $final_proposal->id);
                                    } else {
                                        Final_proposal::where('id', $final_proposal->id)->update(['down_payment' => 0, 'down_payment_value' => 0.00]);
                                        Milestone::where('final_proposal_id', $final_proposal->id)->update(['down_payment' => 0]);
                                    }
                                    $GroupControllerObj=new GroupController;
                                    $GroupMemsObj=new GroupMembersController;
                                    $projectObj=new ProjectController;
                                    $projectInfo = json_decode($projectObj->getProject($req->project_id))->data;
                                    $companyAdmin = $GroupMemsObj->getCompanyAdminByGroupId($projectInfo->company_id);
                                    $agency = $GroupControllerObj->getGroupById($req->team_id);
                                    $adminName = $companyAdmin->first_name .' '. $companyAdmin->last_name;
                                    $details = [
                                        "subject" => 'Final Proposal Submitted By'.$agency->name,
                                        "name" => $adminName,
                                        "project_id" =>  $projectInfo->id,
                                        "project_name" =>  $projectInfo->name,
                                        "Proposal_description"=>$final_proposal->description,
                                        "agency_name"=>$agency->name
                                    ];
                                    Mail::mailer('smtp2')->to($companyAdmin->email)->send(new submitFinalProposal($details));
                                    $response = Controller::returnResponse(200, 'Final proposal add successfully', $final_proposal->id);
                                    return (json_encode($response));
                                } catch (Exception $error) {
                                    $response = Controller::returnResponse(500, 'something wrong', $error->getMessage());
                                    return json_encode($response);
                                }
                            }
                        }
                        //if the proposal exists
                        else {
                            try{
                            $rules = array(
                                "title" => "required",
                                "team_id" => "required|exists:groups,id",
                                "project_id" => "required|exists:projects,id",
                                "proposal_id" => "required|exists:proposals,id",
                                "hourly_rate" => "required|numeric",
                                "starting_date" => "required|date",
                                "down_payment" => "required", //value 0=>no down payment 1=>there is down payment
                                "type" => "required", //1=>regular milestones ,2=>monthly based milestones
                                "milestones" => "required"

                            );
                            $validators = Validator::make($req->all(), $rules);
                            if ($validators->fails()) {
                                $responseData = $validators->errors();
                                $response = Controller::returnResponse(101, "Validation Error", $responseData);
                                return (json_encode($response));
                            } else {
                                if ($ifExist['status'] == -1 || $ifExist['status']  == 3) {
                                    $price = $this->calculatePrice($req->hours, $req->hourly_rate);
                                    $req['price'] = $price;
                                    $milestones = new Milestones;
                                    return response($req);
                                    $milestones_submit = $milestones->SubmitFinal($req->milestones, $ifExist['final_proposal_id'], $req->project_id, $req->hourly_rate);
                                    if ($milestones_submit['code'] == 200) {
                                        if ($req->down_payment['status'] == 1) {
                                            $this->downPaymentHandler($req->down_payment, $ifExist['final_proposal_id']);
                                        } else {
                                            Final_proposal::where('id', $ifExist['final_proposal_id'])->update(['down_payment' => 0, 'down_payment_value' => 0.00]);
                                            Milestone::where('final_proposal_id', $ifExist['final_proposal_id'])->update(['down_payment' => 0]);
                                        }
                                        $update_final = $this->updateQuery($ifExist['final_proposal_id'], $req);
                                        Final_proposal::where('id', $ifExist['final_proposal_id'])->update(['status' => $final_proposal_status]);
                                        $GroupControllerObj=new GroupController;
                                        $GroupMemsObj=new GroupMembersController;
                                        $projectObj=new ProjectController;
                                        $projectInfo = json_decode($projectObj->getProject($req->project_id))->data;
                                        $companyAdmin = $GroupMemsObj->getCompanyAdminByGroupId($projectInfo->company_id);
                                        $agency = $GroupControllerObj->getGroupById($req->team_id);
                                        $adminName = $companyAdmin->first_name .' '. $companyAdmin->last_name;
                                        $desc=Final_proposal::where('id', $ifExist['final_proposal_id'])->select('description')->first()->description;
                                        $details = [
                                            "subject" => 'Final Proposal Submitted By '.$agency->name,
                                            "name" => $adminName,
                                            "project_id" =>  $projectInfo->id,
                                            "project_name" =>  $projectInfo->name,
                                            "Proposal_description"=>$desc,
                                            "agency_name"=>$agency->name
                                        ];
                                        Mail::mailer('smtp2')->to($companyAdmin->email)->send(new SubmitFinalProposal($details));
                                        $response = Controller::returnResponse(200, 'update data successful', []);
                                        return json_encode($response);
                                    } else {
                                        $response = Controller::returnResponse(500, 'something went wrong ', $milestones_submit['msg']);
                                        return json_encode($response);
                                    }
                                } else {
                                    $responseData = $this->selectQuery($ifExist['final_proposal_id']);
                                    $response = Controller::returnResponse(200, 'You already have proposal', $responseData);
                                    return json_encode($response);
                                }
                            }
                        }
                        catch(Exception $error)
                        {
                            $response = Controller::returnResponse(500, "something went wrong",$error->getMessage());
            return (json_encode($response));
                        }
                        }
                    } else {
                        $response = Controller::returnResponse(422, 'your initial proposal status not accepted ', []);
                        return json_encode($response);
                    }
                } else {
                    $response = Controller::returnResponse(422, 'you do not have initial proposal ', []);
                    return json_encode($response);
                }
            } else {
                $response = Controller::returnResponse(422, 'Unauthorized you are trying to access another agency proposal ', []);
                return json_encode($response);
            }
        } else {
            $response = Controller::returnResponse(422, 'Unauthorized action this action for admins only or you do not have team', []);
            return json_encode($response);
        }
    }
    function getProposalType($project_id, $team_id)
    {

        $final_proposal = DB::table('final_proposals')
            ->where('project_id', '=', $project_id)
            ->where('team_id', '=', $team_id)
            ->select('type')
            ->first();
        if ($final_proposal === null) {
            $type = 0;
        } else {
            $type = (int)$final_proposal->type;
        }
        return $type;
    }
    function updateHoursPrice($hours, $hourly_rate, $final_proposal_id)
    {
        try {
            $price = $this->calculatePrice($hours, $hourly_rate);
            Final_proposal::where('id', $final_proposal_id)->update(['hours' => $hours, 'hourly_rate' => $hourly_rate, 'price' => $price]);
        } catch (Exception $error) {
            return ['code' => 500, 'msg' => $error->getMessage()];
        }
    }
    // function updateHoursAndPrice($hours,$hourly_rate)
    // {
    //     $price=$this->calculatePrice($hours,$hourly_rate);
    //     Final_proposal::
    // }
    private function rejectAll($project_id, $proposal_id)
    {
        try {
            $db = DB::table('final_proposals')
                ->where('project_id', '=', $project_id)
                ->where('id', '!=', $proposal_id)
                ->update(['status' => 2]);
            return ['code' => 200, 'msg' => 'success'];
        } catch (Exception $error) {
            return ['code' => 500, 'msg' => $error->getMessage()];
        }
    }
    function acceptFinalProposal(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            if ($userData['exist'] == 1) {
                if ($userData['group_id'] == $req->company_id) {
                    if ($userData['privileges'] == 1) {

                        $rejectAll = $this->rejectAll($req->project_id, $req->proposal_id);
                        if ($rejectAll['code'] == 200) {
                            $final_proposal = Final_proposal::where('id', $req->proposal_id)->select('down_payment', 'team_id')->first();
                            if ($final_proposal->down_payment == 0) {
                                Project::where('id', $req->project_id)->update(['team_id' => $final_proposal->team_id, 'status' => 1]);
                            } else {
                                Project::where('id', $req->project_id)->update(['team_id' => $final_proposal->team_id, 'status' => 4]);
                            }

                            Final_proposal::where('id', $req->proposal_id)->update(['status' => 1]);
                            $final_proposal=Final_proposal::where('id',$req->proposal_id)->select('team_id','project_id')->first();
                            $groupMemsObj = new GroupMembersController;
                            $projectObj = new ProjectController;
                            $agencyAdmin = $groupMemsObj->getTeamAdminByGroupId($final_proposal->team_id);
                            $projectInfo = json_decode($projectObj->getProject($final_proposal->project_id))->data;
                            $adminName = $agencyAdmin->first_name . $agencyAdmin->last_name;
                            $details = [
                                "subject" => 'Your FinalProposal Has Been Accepted',
                                "name" => $adminName,
                                "project_id" =>  $projectInfo->id,
                                "project_name" =>  $projectInfo->name,
                                "type"=>1
                               
                            ];
                            Mail::mailer('smtp2')->to($agencyAdmin->email)->send(new FinalProposalActions($details));
                            $response = Controller::returnResponse(200, "proposal accepted", []);
                            return (json_encode($response));
                        } else {
                            $response = Controller::returnResponse(500, "something wrong", $rejectAll['msg']);
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
            $response = Controller::returnResponse(500, "something wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function rejectFinalProposal(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            if ($userData['exist'] == 1) {
                if ($userData['group_id'] == $req->company_id) {
                    if ($userData['privileges'] == 1) {
                        Final_proposal::where('id', $req->proposal_id)->update(['status' => 2]);
                        $final_proposal=Final_proposal::where('id',$req->proposal_id)->select('team_id','project_id')->first();
                        $groupMemsObj = new GroupMembersController;
                        $projectObj = new ProjectController;
                        $agencyAdmin = $groupMemsObj->getTeamAdminByGroupId($final_proposal->team_id);
                        $projectInfo = json_decode($projectObj->getProject($final_proposal->project_id))->data;
                        $adminName = $agencyAdmin->first_name . $agencyAdmin->last_name;
                        $details = [
                            "subject" => 'Your FinalProposal Has Been Rejected',
                            "name" => $adminName,
                            "project_id" =>  $projectInfo->id,
                            "project_name" =>  $projectInfo->name,
                            "type"=>2
                           
                        ];
                        Mail::mailer('smtp2')->to($agencyAdmin->email)->send(new FinalProposalActions($details));
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
    function reviseFinalProposal(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            if ($userData['exist'] == 1) {
                if ($userData['group_id'] == $req->company_id) {
                    if ($userData['privileges'] == 1) {
                        Final_proposal::where('id', $req->proposal_id)->update(['status' => 3]);
                        $final_proposal=Final_proposal::where('id',$req->proposal_id)->select('team_id','project_id')->first();
                        $groupMemsObj = new GroupMembersController;
                        // $projectObj = new ProjectController;
                        $agencyAdmin = $groupMemsObj->getTeamAdminByGroupId($final_proposal->team_id);
                        // $projectInfo = json_decode($projectObj->getProject($final_proposal->project_id))->data;
                        // $adminName = $agencyAdmin->first_name . $agencyAdmin->last_name;
                        // $details = [
                        //     "subject" => 'Review Your FinalProposal',
                        //     "name" => $adminName,
                        //     "project_id" =>  $projectInfo->id,
                        //     "project_name" =>  $projectInfo->name,
                        //     "type"=>3
                           
                        // ];
                        // Mail::mailer('smtp2')->to($agencyAdmin->email)->send(new FinalProposalActions($details));
                        $response = Controller::returnResponse(200, "Please contact the agency via email ", ['admin_email'=>$agencyAdmin->email]);
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
            $response = Controller::returnResponse(500, "something went wrong",$error->getMessage());
            return (json_encode($response));
        }
    }
    function testtest($id)
    {
        $final_proposal=Final_proposal::where('id',$id)->select('team_id','project_id')->first();
        $groupMemsObj = new GroupMembersController;
        $projectObj = new ProjectController;
        $agencyAdmin = $groupMemsObj->getTeamAdminByGroupId($final_proposal->team_id);
        $projectInfo = json_decode($projectObj->getProject($final_proposal->project_id))->data;
        $adminName = $agencyAdmin->first_name . $agencyAdmin->last_name;
        $details = [
            "subject" => 'Review Your FinalProposal',
            "name" => $adminName,
            "project_id" =>  $projectInfo->id,
            "project_name" =>  $projectInfo->name,
            "type"=>3
           
        ];
        Mail::mailer('smtp2')->to('barbarawiahmad07@gmail.com')->send(new FinalProposalActions($details));
    }
}
