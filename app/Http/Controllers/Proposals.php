<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\InitialProposalActions;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use App\Models\proposal;
use App\Models\Project;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProposalMail;
use App\Models\Countries;
use App\Models\Group;
use App\Models\Group_member;
use App\Models\Proposal_requirement;
use App\Models\Team;
use App\Models\User;


class Proposals extends Controller
{
    //add row 
    function Insert(Request $req)
    {


        try {
            $userData = Controller::checkUser($req);

            if ($userData['privileges'] == 1) {
                if ($userData['group_id'] == $req->team_id) {
                    if ($userData['verified'] == 1) {
                        $rules = array(
                            "team_id" => "required",
                            "user_id" => "required",
                            "project_id" => "required",
                            "price_min" => "required",
                            "price_max" => "required",
                            "from" => "required",
                            "to" => "required",
                            "our_offer" => "required"
                        );
                        $ifExist = $this->checkIfProposalExists($req->project_id, $req->team_id);
                        if ($ifExist['exist'] == 1) {
                            $proposal = proposal::find($ifExist['proposal_id']);
                            $response = Controller::returnResponse(422, 'You already applied to this project', ["propsal" => $proposal]);
                            return json_encode($response);
                        }
                        $validators = Validator::make($req->all(), $rules);
                        if ($validators->fails()) {
                            $responseData = $validators->errors();
                            $response = Controller::returnResponse(101, "Validation Error", $responseData);
                            return (json_encode($response));
                        }
                        $proposal = proposal::create($req->all());
                        $proposal_id = $proposal->id;
                        $responseData = array("proposal_id" => $proposal_id);
                        $response = Controller::returnResponse(200, "proposal added successfully", $responseData);
                        //add send email 
                        $projectData = Project::find($req->project_id);
                        $teamData = Group::find($req->team_id);
                        $companyData = Group::find($projectData->company_id);
                        $companyAdminData = User::find($projectData->user_id);
                        $moreTeamData = Team::select('link', 'country', 'employees_number')->where('group_id', '=', $proposal->team_id)->get()->first();
                        $teamInfo['name'] = $teamData->name;
                        $teamInfo['link'] = $moreTeamData->link;
                        $teamInfo['country'] = Countries::find($moreTeamData->country)->name;
                        // $teamInfo['country'] =$moreTeamData->country;
                        $teamInfo['employees_number'] = $moreTeamData->employees_number;
                        $estPrice = $this->calculateEstimatedPrice($proposal->from, $proposal->to, $proposal->price_min, $proposal->price_max);
                        $details = [
                            'subject' => 'Initial Proposal ' . $projectData->name,
                            'project_name' => $projectData->name,
                            'project_id' => $projectData->id,
                            'team_info' => $teamInfo,
                            'admin_name' => $companyAdminData->first_name,
                            'proposal' => $proposal,
                            'est' => $estPrice
                        ];
                        Mail::mailer('smtp2')->to('hamzahshajrawi@gmail.com')->send(new ProposalMail($details));
                        //Mail::mailer('smtp2')->to($companyAdminData->email)->send(new ProposalMail($details));
                        //Mail::mailer('smtp2')->to('abed@tapflow.app')->send(new ProposalMail($details));
                        //Mail::mailer('smtp2')->to('naser@tapflow.app')->send(new ProposalMail($details));
                        return (json_encode($response));
                    } else {
                        $response = Controller::returnResponse(422, 'You can not apply now, your agency does not verified yet', []);
                        return json_encode($response);
                    }
                } else {
                    $response = Controller::returnResponse(422, 'Unauthorized action this you tried to use another team data ', []);
                    return json_encode($response);
                }
            } else {
                $response = Controller::returnResponse(422, 'Unauthorized action this action for admins only or you do not have team ', []);
                return json_encode($response);
            }
        } catch (Exception $error) {
            $responseData = array("error" => $error->getMessage());
            $response = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
            return (json_encode($response));
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
    function getProposalByProjectAndTeamId($project_id, $team_id)
    {

        $proposal = DB::table('proposals')
            ->where('project_id', '=', $project_id)
            ->where('team_id', '=', $team_id)
            ->first();

        return $proposal;
    }
    function getProjectProposalsById(Request $req, $project_id, $offset, $limit)
    {
        $userData = $req->user();
        $project = Project::where('id', $project_id)->select('user_id')->first();
        $GroupControllerObj = new GroupController;
        $groupMemsObj = new GroupMembersController;

        $project_group_id = $GroupControllerObj->getGroupIdByUserId($project->user_id);
        $user_group_id = $GroupControllerObj->getGroupIdByUserId($userData->id);

        if ($user_group_id == $project_group_id) {

            $page = ($offset - 1) * $limit;
            try {
                $proposals = DB::table('proposals')
                    ->select('id', 'team_id', 'project_id', 'price_min', 'price_max', 'from', 'to', 'our_offer', 'status', 'created_at')
                    ->where('project_id', $project_id)
                    ->distinct()
                    ->latest()->offset($page)->limit($limit)
                    ->get();
                $proposalsCounter = DB::table('proposals')
                    ->select('id', 'team_id', 'project_id', 'price_min', 'price_max', 'from', 'to', 'our_offer', 'status', 'created_at')
                    ->where('project_id', $project_id)
                    ->distinct()
                    ->count();

                foreach ($proposals as $proposal) {
                    $proposal->agency_info =  $GroupControllerObj->getGroupNameAndImage($proposal->team_id);
                    $agencyAdmin = $groupMemsObj->getTeamAdminByGroupId($proposal->team_id);
                    $proposal->agency_info->admin_email = $agencyAdmin->email;
                    $priceMin = (float)$proposal->price_min * (float)$proposal->from;
                    $priceMax = (float)$proposal->price_max * (float)$proposal->to;
                    $proposal->price_min = $priceMin;
                    $proposal->price_max = $priceMax;
                }
                $responseData = array('allData' => $proposals, 'counter' => $proposalsCounter);
                $response = Controller::returnResponse(200, "successful", $responseData);
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
    function checkIfProposalExists($project_id, $team_id)
    {
        $proposal_id = DB::table('proposals')
            ->select('id', 'status')
            ->where('team_id', '=', $team_id)
            ->where('project_id', '=', $project_id)
            ->first();
        if ($proposal_id == null) {
            return ['exist' => 0];
        } else {
            return ['exist' => 1, "proposal_id" => $proposal_id->id, "status" => $proposal_id->status];
        }
    }
    function getProposalInfo($project_id, $team_id)
    {

        $proposal = DB::table('proposals')
            ->select('*')
            ->where('team_id', '=', $team_id)
            ->where('project_id', '=', $project_id)
            ->first();

        if ($proposal == null) {
            return ['exist' => 0];
        } else {
            return ['exist' => 1, "proposal" => $proposal];
        }
    }
    private function calculateEstimatedPrice($from, $to, $min, $max)
    {
        $estimatedPrice['min'] = $from * $min;
        $estimatedPrice['max'] = $to * $max;
        return $estimatedPrice;
    }

    function acceptProposal(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            if ($userData['exist'] == 1) {
                if ($userData['group_id'] == $req->company_id) {
                    if ($userData['privileges'] == 1) {

                        Proposal::where('id', $req->proposal_id)->update(['status' => 1]);
                        // notify agency
                        $mail = $this->notifyAgency($req->proposal_id, 1);
                        $response = Controller::returnResponse(200, "proposal accepted", []);
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
            $response = Controller::returnResponse(500, "something wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function rejectProposal(Request $req)
    {
        $userData = Controller::checkUser($req);
        if ($userData['exist'] == 1) {
            if ($userData['group_id'] == $req->company_id) {
                if ($userData['privileges'] == 1) {
                    Proposal::where('id', $req->proposal_id)->update(['status' => 2]);
                    // notify agency
                    $mail = $this->notifyAgency($req->proposal_id, 2);
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

    function getClientPropsals(Request $req, $offset = 1, $limit = 3)
    {
        try {
            $userData = Controller::checkUser($req);
            $page = ($offset - 1) * $limit;
            $propsals = $this->getProposaldata(DB::table('proposals')
                ->leftJoin('final_proposals', function ($join) {
                    $join->on('proposals.id', '=', 'final_proposals.proposal_id')
                        ->where('final_proposals.status', '=', 0);
                })
                ->join('projects', 'proposals.project_id', '=', 'projects.id')
                ->select('proposals.*', 'projects.name as projectName', 'final_proposals.price as finalPrice', 'final_proposals.status as finalStatus', 'final_proposals.id as finalId')
                ->where('projects.company_id', '=', $userData['group_id'])
                ->latest()->offset($page)->limit($limit)
                ->distinct()
                ->get());
            // return $propsals;
            $response = Controller::returnResponse(200, "data found", $propsals);
            return (json_encode($response));
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "there is an error", $error->getMessage());
            return (json_encode($response));
        }
    }
    private function getProposaldata($proposals)
    {
        $groupObj = new GroupController;
        foreach ($proposals as &$proposal) {
            $teamInfo = $groupObj->getGroupNameAndImage($proposal->team_id);
            $proposal->teamName = $teamInfo->name;
            $proposal->teamImage = $teamInfo->image;
            $proposalEstPrice = $this->calculateEstimatedPrice($proposal->from, $proposal->to, $proposal->price_min, $proposal->price_max);
            $proposal->estMin = $proposalEstPrice['min'];
            $proposal->estMax = $proposalEstPrice['max'];
            // if (!isset($proposal->finalId)) {
            //     $proposal->finalPrice = $proposal->finalRate * $proposal->finalHours;
            // } else {
            //     $proposal->finalPrice = '';
            // }
        }

        return $proposals;
    }
    function notifyAgency($proposalId, $status)
    {
        $groupMemberObj = new GroupMembersController;
        $proposal = Proposal::where('id', $proposalId)->get()->first();
        $teamId = $proposal->team_id;
        $projectId = $proposal->project_id;
        $admin = $groupMemberObj->getTeamAdminByGroupId($teamId);
        $project = Project::where('id', '=', $projectId)->get()->first();
        $groupId = $project->company_id;
        $member = Group_member::where('group_id', '=', $groupId)->where('privileges', '=', 1)->get()->first();
        $clientId = $member->user_id;
        $clinet = User::where('id', $clientId)->get()->first();
        $subject = $project->name . " Proposal Update";
        $details = array(
            'subject' => $subject,
            'projectName' => $project->name,
            'clientEmail' => $clinet->email,
            'status' => $status,
        );
        return Mail::mailer('smtp2')->to($admin->email)->send(new InitialProposalActions($details));
        // dd($details);
        //return Mail::mailer('smtp2')->to('hamzahshajrawi@gmail.com')->send(new InitialProposalActions($details));
    }
    function addProposalHireDev(Request $req)
    {
        $userData = $this->checkUser($req);
        $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1;
        if (!$condtion) {
            $response = Controller::returnResponse(401, "Unauthrized", []);
            return (json_encode($response));
        }
        try {
            $rules = array(
                "project_id" => "required",
                "requirements" => "required",
                "our_offer" => "required",
            );
            $validators = Validator::make($req->all(), $rules);
            if ($validators->fails()) {
                $responseData = $validators->errors();
                $response = Controller::returnResponse(101, "Validation Error", $responseData);
                return (json_encode($response));
            }

            $requirementObj = new Requirement;
            $projectId = $req->project_id;
            $teamId = $userData['group_id'];
            $userId = $userData['user_id'];
            $requirements = json_decode($req->requirements);

            $project = Project::where('id', '=', $projectId)->first();
            if (!$project) {
                $response = Controller::returnResponse(422, 'Project does not exsist', []);
                return (json_encode($response));
            }
            if ($project->type != 3) {
                $response = Controller::returnResponse(422, 'this function not possible for this type of projects', []);
                return (json_encode($response));
            }

            $proposalDoesExsist = Proposal::where('project_id', '=', $projectId)->where('team_id', '=', $teamId)->first();
            if ($proposalDoesExsist) {
                $response = Controller::returnResponse(422, 'You already applied to this project', ["propsal" => $proposalDoesExsist]);
                return (json_encode($response));
            }

            /**
             * check requirements ids if valid
             */
            $projectRequirements = $requirementObj->getRequirementsAndHourlyRateByProjectId($projectId);
            $projectRequirements = $projectRequirements->toArray();
            $projectRequirementsIds = array_column($projectRequirements, 'requirementId');
            $requirementsIds = array_column($requirements, 'requirementId');
            if ($requirementsIds != $projectRequirementsIds) {
                $response = Controller::returnResponse(101, 'Requirments not right', []);
                return (json_encode($response));
            }


            $proposalArr = array(
                'team_id' => $teamId,
                'user_id' => $userId,
                'project_id' => $projectId,
                'price_min' => 0,
                'price_max' => 0,
                'from' => 0,
                'to' => 0,
                'our_offer' => $req->our_offer,
            );
            $proposal = proposal::create($proposalArr);
            foreach ($requirements as $keyRequ => $valRequ) {
                $requirementArr = array(
                    'proposal_id' => $proposal->id,
                    'requirement_id' => $valRequ['requirementId'],
                    'hourly_rate' => $valRequ['hourlyRate'],
                );
                Proposal_requirement::create($requirementArr);
            }
            $responseData = array("proposal_id" => $proposal->id);
            $response = Controller::returnResponse(200, "proposal added successfully", $responseData);
            /** 
             * send email
            */
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "there is an error", $error->getMessage());
            return (json_encode($response));
        }
    }
}
