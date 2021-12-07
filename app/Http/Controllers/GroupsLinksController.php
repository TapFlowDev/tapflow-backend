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
            "group_id"=>"required|exists:groups,id",
            "links"=>"required|array"
          
        );
        $validator=Validator::make($req->all(),$rules);
        if($validator->fails()){
            $responseData = $validator->errors();
            $response = Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        }
        else{
            try{
        $links=$req->links;
        $del_links=Groups_link::where('group_id',$req->group_id)->delete();
        foreach($links as $link)
        {
            $add_links=Groups_link::create(["group_id"=>$req->group_id,"link"=>$link]);
        }
        
        $response = Controller::returnResponse(200, 'updated successfully', $Array=array());
        return json_encode($response);
            }
            catch(Exception $error)
            {
                $response = Controller::returnResponse(500, 'There IS Error Occurred', $error);
                return json_encode($response);
            }
    }       
}
}

