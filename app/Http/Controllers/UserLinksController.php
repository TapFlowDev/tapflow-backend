<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User_link;

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
