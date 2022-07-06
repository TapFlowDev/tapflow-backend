<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\InitialProposalActions;
use App\Mail\ProposalMail;
use App\Models\Countries;
use App\Models\Group;
use App\Models\Group_member;
use App\Models\hire_developer_final_proposal;
use App\Models\hire_developer_proposals;
use App\Models\Project;
use App\Models\Proposal_requirement;
use App\Models\resources;
use App\Models\Team;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class HireDeveloperProposalsController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        // $response = Controller::returnResponse(500, "Validation Error", $req->all());
        // return (json_encode($response));
        $userData = $this->checkUser($req);
        $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1 && $userData['verified'] == 1;
        if (!$condtion) {
            $response = Controller::returnResponse(401, "Unauthrized", []);
            return (json_encode($response));
        }
        try {
            $rules = array(
                "project_id" => "required",
                "requirements" => "required",
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
            $requirements = $req->requirements;

            $project = Project::where('id', '=', $projectId)->first();
            if (!$project) {
                $response = Controller::returnResponse(422, 'Project does not exsist', []);
                return (json_encode($response));
            }
            if ($project->type != 3) {
                $response = Controller::returnResponse(422, 'this function not possible for this type of projects', []);
                return (json_encode($response));
            }

            $proposalDoesExsist = hire_developer_proposals::where('project_id', '=', $projectId)->where('team_id', '=', $teamId)->first();
            if ($proposalDoesExsist) {
                $response = Controller::returnResponse(422, 'You already applied to this project', ["propsal" => $proposalDoesExsist]);
                return (json_encode($response));
            }

            /**
             * check requirements ids if valid
             */
            // $projectRequirements = $requirementObj->getRequirementsAndHourlyRateByProjectId($projectId);
            // $projectRequirements = $projectRequirements->toArray();
            // $projectRequirementsIds = array_column($projectRequirements, 'requirementId');
            // $requirementsIds = array_column($requirements, 'requirementId');
            // if ($requirementsIds != $projectRequirementsIds) {
            //     $response = Controller::returnResponse(101, 'Requirments not right', []);
            //     return (json_encode($response));
            // }


            $proposalArr = array(
                'team_id' => $teamId,
                'user_id' => $userId,
                'project_id' => $projectId,
                'details' => $req->our_offer,
            );
            $proposal = hire_developer_proposals::create($proposalArr);
            foreach ($requirements as $keyRequ => $valRequ) {
                $requirementArr = array(
                    'proposal_id' => $proposal->id,
                    'requirement_id' => $keyRequ,
                    'hourly_rate' => $valRequ,
                );
                Proposal_requirement::create($requirementArr);
            }
            $responseData = array("proposal_id" => $proposal->id);
            $response = Controller::returnResponse(200, "proposal added successfully", $responseData);
            return json_encode($response);

            /** 
             * send email
             */
            $projectData = $project;
            $teamData = Group::find($req->team_id);
            $companyData = Group::find($projectData->company_id);
            $companyAdminData = User::find($projectData->user_id);
            $moreTeamData = Team::select('link', 'country', 'employees_number')->where('group_id', '=', $proposal->team_id)->get()->first();
            $teamInfo['name'] = $teamData->name;
            $teamInfo['link'] = $moreTeamData->link;
            $teamInfo['country'] = Countries::find($moreTeamData->country)->name;
            // $teamInfo['country'] =$moreTeamData->country;
            $teamInfo['employees_number'] = $moreTeamData->employees_number;
            // $estPrice = $this->calculateEstimatedPrice($proposal->from, $proposal->to, $proposal->price_min, $proposal->price_max);
            $userInfo = User::find($userData['user_id']);
            $details = [
                'subject' => 'New Application ' . $projectData->name,
                'project_name' => $projectData->name,
                'project_type' => $projectData->type,
                'project_id' => $projectData->id,
                'team_info' => $teamInfo,
                'admin_name' => $companyAdminData->first_name,
                'proposal' => $proposal,
                'agency_admin_name' => "$userInfo->first_name $userInfo->last_name",
                // 'est' => $estPrice
            ];

            Mail::mailer('smtp2')->to('hamzahshajrawi@gmail.com')->send(new ProposalMail($details));
            $fenLink="/Client-user/main/project-info/".$projectData->id;
            Controller::sendNotification($projectData->company_id, $projectData->name, 'You have received a new Application', $fenLink, 2, 'hire_developer_proposals',  $proposal->id);
            //Mail::mailer('smtp2')->to($companyAdminData->email)->send(new ProposalMail($details));
            //Mail::mailer('smtp2')->to('abed@tapflow.app')->send(new ProposalMail($details));
            //Mail::mailer('smtp2')->to('naser@tapflow.app')->send(new ProposalMail($details));

            return (json_encode($response));
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "there is an error", $error->getMessage());
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
    function checkIfProposalExists($project_id, $team_id)
    {
        $proposal_id = DB::table('hire_developer_proposals')
            ->select('id', 'status')
            ->where('team_id', '=', $team_id)
            ->where('project_id', '=', $project_id)
            ->first();
        if ($proposal_id == null) {
            return ['exist' => 0, "status" => null];
        } else {
            return ['exist' => 1, "proposal_id" => $proposal_id->id, "status" => $proposal_id->status];
        }
    }
    function getProposalsByProjectIdTeamId($projectId, $teamId = 0, $page = 1, $limit = 4)
    {
        $conditionArray = [
            ['project_id', '=', $projectId]
        ];
        if ($teamId > 0) {
            $conditionArray[] = ['team_id', '=', $teamId];
            $proposals = hire_developer_proposals::where($conditionArray)->distinct()->latest()->offset($page)->limit($limit)->get();
            $proposalCount = hire_developer_proposals::where($conditionArray)->count();
            // $proposalData = $this->getData($proposals);
        } else {
            $proposals = hire_developer_proposals::where($conditionArray)->distinct()->latest()->offset($page)->limit($limit)->get();
            $proposalCount = hire_developer_proposals::where($conditionArray)->count();
        }
        $proposalData = $this->getData($proposals);
        $returnData = [
            'allData' => $proposalData,
            'count' => $proposalCount
        ];
        return $returnData;
    }
    private function getData($proposals)
    {
        $requirementObj = new Requirement;
        $teamControllersObj = new TeamController;

        foreach ($proposals as $keyP => &$proposal) {
            $proposal->requirments_description = $requirementObj->getRequirementsByProjectId($proposal->project_id)->pluck('description')->toArray();
            $proposalRequirments = $requirementObj->getHireDevInitialProposalRequirements($proposal->id);
            $proposal->requirementDetails = $proposalRequirments;
            $teamInfo = $teamControllersObj->get_team_info($proposal->team_id);
            $userInfo = User::find($proposal->user_id);
            $teamCountry = Countries::find($teamInfo->country);
            $teamArr = array(
                'teamName' => $teamInfo->name,
                'teamAdminName' => $userInfo->name,
                'teamCountry' => ($teamCountry ? $teamCountry->name : "unset"),
                'teamFlag' => ($teamCountry ? $teamCountry->flag : ""),
            );
            $proposal->teamInfo = $teamArr;
        }
        return $proposals;
    }
    function getCountByProjectId($projectId)
    {
        $conditionArray = [
            ['project_id', '=', $projectId]
        ];
        $proposalCount = hire_developer_proposals::where($conditionArray)->count();
        return $proposalCount;
    }
    function getContractsCountByProjectId($projectId)
    {
        $conditionArray = [
            ['project_id', '=', $projectId]
        ];
        $proposalsIds = hire_developer_proposals::select('id')->where($conditionArray)->pluck('id')->toArray();
        $proposalCount = hire_developer_final_proposal::whereIn('proposal_id', $proposalsIds)->where('status', '>', -1)->count();
        return $proposalCount;
    }
    function getHiresCountByProjectId($projectId)
    {
        $conditionArray = [
            ['project_id', '=', $projectId]
        ];
        $proposalsIds = hire_developer_proposals::select('id')->where($conditionArray)->pluck('id')->toArray();
        $finalProposalsIds = hire_developer_final_proposal::select('id')->where('status', '=', 1)->whereIn('proposal_id', $proposalsIds)->pluck('id')->toArray();
        $resourcesCount = resources::whereIn('contract_id', $finalProposalsIds)->count();
        return $resourcesCount;
    }
    function rejectProposal(Request $req)
    {
        $userData = Controller::checkUser($req);
        $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1;
        if (!$condtion) {
            $response = Controller::returnResponse(401, "Unauthrized", []);
            return (json_encode($response));
        }
        $proposal = hire_developer_proposals::select('id', 'status', 'project_id')->where('id', $req->proposal_id)->first();
        if (!$proposal) {
            $response = Controller::returnResponse(422, 'Proposal does not exsist', []);
            return (json_encode($response));
        }
        $project = Project::select('id')->where('id', '=', $proposal->project_id)->where('company_id', '=', $userData['group_id'])->first();
        if (!$project) {
            $response = Controller::returnResponse(422, 'Project does not exsist', []);
            return (json_encode($response));
        }
        if ($proposal->status > 0) {
            $response = Controller::returnResponse(422, 'Action denied', []);
            return (json_encode($response));
        }
        $proposal->status = 2;
        $proposal->save();

        // notify agency
        $mail = $this->notifyAgency($req->proposal_id, 2);
        $fenLink="/a-user/main/project/".$project->id;
        Controller::sendNotification($project->company_id, $project->name, 'Your application rejected', $fenLink, 2, 'hire_developer_proposals',$req->proposal_id);
        $response = Controller::returnResponse(200, "proposal rejected", []);
        return (json_encode($response));
    }
    function acceptProposal(Request $req)
    {
        $userData = Controller::checkUser($req);
        $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1;
        if (!$condtion) {
            $response = Controller::returnResponse(401, "Unauthrized", []);
            return (json_encode($response));
        }
        $proposal = hire_developer_proposals::select('id', 'status', 'project_id','user_id')->where('id', $req->proposal_id)->first();
        if (!$proposal) {
            $response = Controller::returnResponse(422, 'Proposal does not exsist', []);
            return (json_encode($response));
        }
        $project = Project::select('*')->where('id', '=', $proposal->project_id)->where('company_id', '=', $userData['group_id'])->first();
        if (!$project) {
            $response = Controller::returnResponse(422, 'Project does not exsist', []);
            return (json_encode($response));
        }
        if ($proposal->status > 0) {
            $response = Controller::returnResponse(422, 'Action denied', []);
            return (json_encode($response));
        }
        $proposal->status = 1;
        $proposal->save();
        $projectObj=new ProjectController;
        $RoomObj=new RoomController();
        $companyAdmin=$projectObj->getCompanyProjectAdmin($proposal->project_id);
        $data=array('name'=>null,'agencyAdmin'=>$proposal->user_id,'companyAdmin'=>$companyAdmin);
        $room=$RoomObj->createRoom($data);
        if($room['code'] != 200)
        {
            $response = Controller::returnResponse(500, "something wrong chat", $room['msg']);
            return (json_encode($response));
        }
        // notify agency
        $mail = $this->notifyAgency($req->proposal_id, 1);
        $fenLink="/a-user/main/project/".$project->id;
        Controller::sendNotification($project->company_id, $project->name, 'Your application accepted', $fenLink, 2, 'hire_developer_proposals',$req->proposal_id);
        $response = Controller::returnResponse(200, "proposal accepted", []);
        return (json_encode($response));
    }
    function notifyAgency($proposalId, $status)
    {
        $groupMemberObj = new GroupMembersController;
        $proposal = hire_developer_proposals::where('id', $proposalId)->get()->first();
        $teamId = $proposal->team_id;
        $projectId = $proposal->project_id;
        $admin = $groupMemberObj->getTeamAdminByGroupId($teamId);
        $project = Project::where('id', '=', $projectId)->get()->first();
        $groupId = $project->company_id;
        $member = Group_member::where('group_id', '=', $groupId)->where('privileges', '=', 1)->get()->first();
        $clientId = $member->user_id;
        $clinet = User::where('id', $clientId)->get()->first();
        $subject = $project->name . " Application Update";
        $details = array(
            'subject' => $subject,
            'projectName' => $project->name,
            'projectType' => $project->type,
            'clientEmail' => $clinet->email,
            'status' => $status,
        );
        return Mail::mailer('smtp2')->to($admin->email)->send(new InitialProposalActions($details));
        // dd($details);
        //return Mail::mailer('smtp2')->to('hamzahshajrawi@gmail.com')->send(new InitialProposalActions($details));
    }
    function getAcceptedProposalByProjectId($projectId, $agencyId = 0)
    {
        $conditionArray[] = ['project_id', '=', $projectId];
        $conditionArray[] = ['status', '=', 1];
        if ($agencyId > 0) {
            $conditionArray[] = ['team_id', '=', $agencyId];
        }
        return hire_developer_proposals::select('id')->where($conditionArray)->pluck('id')->toArray();
    }
}
