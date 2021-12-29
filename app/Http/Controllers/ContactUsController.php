<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact_Us;
use Exception;
use Illuminate\Support\Facades\Validator;
class ContactUsController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        $rules=array(
            'name'=>'required|max:255',
            'email'=>'required|max:255',
            'message'=>'required',
        );
        $validator=Validator::make($req->all(),$rules);
        if($validator->fails())
        {   
            $response=Controller::returnResponse(101,'Validation Error',$validator->errors());
            return(json_encode($response));
        }
        else{
            try{
            $query=Contact_Us::create($req->all());
            $response=Controller::returnResponse(200,'successful',[]);
            return(json_encode($response));
        }catch(Exception $error){
            $response=Controller::returnResponse(500,'something went wrong',$error);
            return(json_encode($response));
        }
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
}