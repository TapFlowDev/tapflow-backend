<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\User_attachment;


class UserAttachmentsController extends Controller
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
    function update_attachment(Request $req)
    {
        $add=$req->add;
        $remove=$req->remove;
        if(count($add)>0)
        {
           foreach($add as $attachment)
           {
               $arr=array
               (
                   "user_id"=>$req->user_id,
                   "attachment"=>$attachment,
               );
                $userLinks= User_attachment::create($arr);
           }
        }
       
        if(count($remove)>0)
        {
            foreach($remove as $attachment)
           {
            
                $userLinks= User_attachment::where('id', $attachment)->delete();
               

           }
        }
        $response = Controller::returnResponse(200, 'updated successfully', $Array=array());
        return json_encode($response);
    }
   
}
