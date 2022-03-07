<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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
use App\Models\Team;
use App\Models\User;

class Proposals extends Controller
{
    //add row 
    function Insert(Request $req)
    {
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
        if($ifExist['exist']==1){
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

        try {
            // $userData = $req->user();
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
            $details = [
                'subject' => 'Initial Proposal '.$projectData->name,
                'project_name' => $projectData->name,
                'team_info' => $teamInfo,
                'admin_name' => $companyAdminData->first_name,
                'proposal' => $proposal
            ];
            
            //Mail::mailer('smtp2')->to('hamzahshajrawi@gmail.com')->send(new ProposalMail($details));
            Mail::mailer('smtp2')->to($companyAdminData->email)->send(new ProposalMail($details));
            Mail::mailer('smtp2')->to('abed@tapflow.app')->send(new ProposalMail($details));
            Mail::mailer('smtp2')->to('naser@tapflow.app')->send(new ProposalMail($details));
            return (json_encode($response));
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
    function getProjectProposalsById(Request $req, $project_id, $offset,$limit)
    {
        $userData = $req->user();
        $project = Project::where('id', $project_id)->select('user_id')->first();
        $GroupControllerObj = new GroupController;
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
                    foreach ($proposals as$proposal)
                    {
                        $proposal->agency_info =  $GroupControllerObj->getGroupNameAndImage($proposal->team_id);
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
    function checkIfProposalExists($project_id, $team_id)
    {
        $final_proposal_id = DB::table('proposals')
            ->select('id')
            ->where('team_id', '=', $team_id)
            ->where('project_id', '=', $project_id)
            ->first();
        if ($final_proposal_id == null) {
            return ['exist' => 0];
        } else {
            return ['exist' => 1, "proposal_id" => $final_proposal_id->id];
        }
    }
}
