<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Team;
use App\Models\Group;
use App\Http\Controllers\GroupsLinksController;
use App\Http\Controllers\GroupMembersController;
use Illuminate\Support\Facades\Validator;
use League\CommonMark\Context;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Exception;
use function PHPSTORM_META\type;

class TeamController extends Controller
{
    //add row 
    function Insert($arr)
    {
        return Team::create($arr);
    }
    //update row according to row id
    function Update($id)
    {

    }
    //delete row according to row id
    function Delete($id)
    {

    }
    function updateFiles($id, $fileName, $columnName){
        Team::where('group_id', $id)->update(array($columnName => $fileName));

    }
     function get_team($id)
    {
        try{
        $linksController=new GroupsLinksController;
        $GroupMembersController=new GroupMembersController;
        $links=$linksController->get_group_links($id);
        $teamMembers=$GroupMembersController->getTeamMembersByGroupId($id);
        $info=$this->get_team_info($id);
        $info->links=$links; 
        $info->teamMembers=$teamMembers; 
        $response=Controller::returnResponse(200, "successful", $info);
        return (json_encode($response));
        }
        catch(Exception $error)
        {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error);
            return json_encode($response);
        }
    }  
    private function get_team_info($id)
    {
        
        $team = DB::table('groups')
        ->Join('teams', 'groups.id', '=', 'teams.group_id')
        ->where('groups.id', '=',  $id)
        ->first();
        
        return ($team);
    }
    function updateGeneralInfo(Request $req)
    {
        $rules=array(
            'id'=>"required|exists:groups,id",
            'name'=>"required|max:255",
            'employees_number'=>"required|gt:1|lt:100",
            'country'=>"required|max:255",
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
            return json_encode($response);
        }
        else
        {
            $group=Group::where("id",$req->id)->update(['name'=>$req->name]);
            $team=Team::where("group_id",$req->id)->update(['country'=>$req->country,'employees_number'=>$req->employees_number]);
            $response = Controller::returnResponse(200, 'successful', $data=array());
            return json_encode($response);
        }
    }
    function updateLink(Request $req)
    {
        $rules=array(
            "id"=>"required|exists:groups,id",
            "link"=>"required|max:255"
        );
        $validator=Validator::make($req->all(),$rules);
        if($validator->fails())
        {
            $response=Controller::returnResponse(101,'Validation Error',$validator->errors());
            return json_encode($response);
        }
        else
        {
            $team=Team::where('group_id',$req->id)->update(['link'=>$req->link]);
            $response=Controller::returnResponse(200,'successful',$arr=array());
            return json_encode($response);
        }
    }
}
