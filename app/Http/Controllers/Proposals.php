<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use App\Models\proposal;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProposalMail;
use App\Models\Group;
use App\Models\Project;

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
            $details = [
                'subject' => 'New project application.',
                'project_name' => $projectData->name,
                'team_name' => $teamData->name,
                'company_name' => $companyData->name 
            ];
            
            Mail::to('hamzahshajrawi@gmail.com')->send(new ProposalMail($details));
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
    function getProposalByProjectAndTeamId($project_id,$team_id)
    {
    
        $proposal=DB::table('proposals')
        ->where('project_id','=',$project_id)
        ->where('team_id','=',$team_id)
        ->first();
       
        return $proposal;
    }
}
