<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User_link;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;

class UserLinksController extends Controller
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
    function update_links(Request $req)
    {
        $rules=array(
            "user_id"=>"required|exists:users,id",
        );
        $validator=Validator::make($req->all(),$rules);
        if($validator->fails()){
            $responseData = $validator->errors();
            $response = Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        }
        else{
            try{
        if(isset($req->links)){ $links=$req->links;}
        else{User_link::where('user_id',$req->user_id)->delete();}
        User_link::where('user_id',$req->user_id)->delete();
        if(count($links)==0){
            $response = Controller::returnResponse(200, 'deleted successfully', []);
            return json_encode($response);
        }
        else{
        foreach($links as $link)
        {
            User_link::create(["user_id"=>$req->user_id,"link"=>$link]);
        }
        }
        $response = Controller::returnResponse(200, 'updated successfully', []);
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
