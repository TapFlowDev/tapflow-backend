<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User_link;
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
                   "user_id"=>$req->user_id,
                   "link"=>$link,
               );
                $userLinks= User_link::create($arr);
           }
        }
       
        if(count($remove)>0)
        {
            foreach($remove as $link)
           {
            
                $userLinks= User_link::where('id', $link)->delete();
               

           }
        }
        $response = Controller::returnResponse(200, 'updated successfully', $Array=array());
        return json_encode($response);
    }
}
}
