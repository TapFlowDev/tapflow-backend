<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Final_proposal;
use Exception;

class Final_proposals extends Controller
{

    //add row 
    function Insert(Request $req)
    {
        $milestone = new Milestones;
        $rules = array(
            "team_id" => "required",
            "project_id" => "required",
            "proposal_id" => "required",
            "price" => "required",
            "days" => "required",
            "milestones" => "required",

        );
        $validators = Validator::make($req->all(), $rules);
        if ($validators->fails()) {
            $responseData = $validators->errors();
            $response = Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        } else {    
            try {
                $price = $req->price;
                $dividable = fmod($price, 5);
                if ($dividable == 0) {
                    $final_proposal = Final_proposal::create($req->except(['milestones']));
                    $final_proposal_id = $final_proposal->id;
                    $milestones = $milestone->Insert($req->milestones, $req->project_id, $final_proposal_id, $req->price);
                    if ($milestones == 101) {
                        $del=Final_proposal::where('id',  $final_proposal_id)->delete();
                        $response = Controller::returnResponse(422, 'the milestone percentage  should be multiples of 5', ['value'=>$milestones]);
                        return json_encode($response);
                    }
                    elseif($milestones == 500 )
                    {
                        $del=Final_proposal::where('id', $final_proposal_id)->delete();
                        $response = Controller::returnResponse(500, 'something wrong', ["error"=>'add milestone','value'=>$milestones] );
                        return json_encode($response);           
                    }
                    $responseData = array(
                        "Final_proposal_id" => $final_proposal_id,
                    );
                    $response = Controller::returnResponse(200, 'Final proposal add successfully', [$responseData,'value'=>$milestones]);
                    return (json_encode($response));
                } else {
                    $response = Controller::returnResponse(422, 'the price should be multiples of 5', []);
                    return json_encode($response);
                }
            } catch (Exception $error) {
                $response = Controller::returnResponse(500, 'something wrong', $error);
                return json_encode($response);
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
