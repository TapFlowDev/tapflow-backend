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
        $del_links=User_link::where('user_id',$req->user_id)->delete();
        $add_links=User_link::create($req->links);
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
