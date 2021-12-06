<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Groups_link;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Exception;

class GroupsLinksController extends Controller
{
    //add row 
    function Insert(Request $req)
    {

    }
    //update row according to row id
    function Update($id)
    {

    }
    //delete row according to row id
    function Delete($id)
    {

    }
     function get_group_links($id)
    {
        try{
        $links=Groups_link::select('link')->where('group_id',$id)->get();  
       
        if(count($links)>0)
        {    $data=array_column($links->toArray(), 'link');
        return($data);
        }
    else{return([]);}
        }
        catch(Exception $error)
        {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error);
            return json_encode($response);
        }
    }
    function updateTeamLinks(Request $req)
    {
        $rules=array(
            "id"=>"required|exists:groups,id",
            "add"=>"required|array",
            "remove"=>"required|array"
        );
        $validator=Validator::make($req->all(),$rules);
        if($validator->fails()){
            $responseData = $validator->errors();
            $response = Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        }
        else{
        $add=$req->add;
        $remove=$req->remove;
        if(count($add)>0)
        {
           foreach($add as $link)
           {
               $arr=array
               (
                   "group_id"=>$req->id,
                   "link"=>$link,
               );
                $groupLinks= Groups_link::create($arr);
           }
        }
       
        if(count($remove)>0)
        {
            foreach($remove as $link)
           {
            
                $groupLinks= Groups_link::where('id', $link)->delete();
           }
        }
        $response = Controller::returnResponse(200, 'updated successfully', $Array=array());
        return json_encode($response);
    }
}
}

