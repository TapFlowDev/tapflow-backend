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
            "title" => "required",  
            "team_id" => "required|exists:groups,id",
            "project_id" => "required|exists:projects,id",
            "proposal_id" => "required|exists:proposals,id",
            "price" => "required",
            "days" => "required",
            "starting_date" => "required|date",
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
                        $del = Final_proposal::where('id',  $final_proposal_id)->delete();
                        $response = Controller::returnResponse(422, 'the milestone percentage  should be multiples of 5', ['value' => $milestones]);
                        return json_encode($response);
                    } elseif ($milestones == 500) {
                        $del = Final_proposal::where('id', $final_proposal_id)->delete();
                        $response = Controller::returnResponse(500, 'something wrong', ["error" => 'add milestone', 'value' => $milestones]);
                        return json_encode($response);
                    }
                    $responseData = array(
                        "Final_proposal_id" => $final_proposal_id,
                    );
                    $response = Controller::returnResponse(200, 'Final proposal add successfully', [$responseData, 'value' => $milestones]);
                    return (json_encode($response));
                } else {
                    $response = Controller::returnResponse(422, 'the price should be multiples of 5', []);
                    return json_encode($response);
                }
            } catch (Exception $error) {
                $response = Controller::returnResponse(500, 'something wrong', $error->getMessage());
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
    function getProposalDetailsById($id)
    {
        $milestone = new Milestones;
        $final_proposal = Final_proposal::select('id', 'team_id', 'project_id', 'price', 'days', 'description')
            ->where('id', $id)->first();
        $milestones = $milestone->getMilestoneByProposalId($id);
        $final_proposal->milestones = $milestones;
        return json_encode ($final_proposal);
    }
    function updateFinalProposal(Request $req)
    {
        $final_proposal_id=$req->final_proposal_id;
        $del_proposal=Final_proposal::where('id',$final_proposal_id)->delete();
        $this->Insert($req);
        return ('success');
    }
    function saveFinalProposal(Request $req)
    {
        $milestone = new Milestones;
        $rules = array(
           
            "team_id" => "required|exists:groups,id",
            "project_id" => "required|exists:projects,id",
            "proposal_id" => "required|exists:proposals,id",
            "starting_date" => "date",
        );
        $validators = Validator::make($req->all(), $rules);
        if ($validators->fails()) {
            $responseData = $validators->errors();
            $response = Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        } else {
            try {
                $price = $req->price;
                if($price !=null){
                $dividable = fmod($price, 5);
                if ($dividable == 0) {
                    
                    $final_proposal = Final_proposal::create($req->except(['milestones']));
                    $final_proposal_id = $final_proposal->id;
                    if($req->milestones != ' ' ||$req->milestones != null ){
                    $milestones = $milestone->Insert($req->milestones, $req->project_id, $final_proposal_id, $req->price);
                    }
                    if ($milestones == 101) {
                        $del = Final_proposal::where('id',  $final_proposal_id)->delete();
                        $response = Controller::returnResponse(422, 'the milestone percentage  should be multiples of 5', ['value' => $milestones]);
                        return json_encode($response);
                    } elseif ($milestones == 500) {
                        $del = Final_proposal::where('id', $final_proposal_id)->delete();
                        $response = Controller::returnResponse(500, 'something wrong', ["error" => 'add milestone', 'value' => $milestones]);
                        return json_encode($response);
                    }
                    $responseData = array(
                        "Final_proposal_id" => $final_proposal_id,
                    );
                    $response = Controller::returnResponse(200, 'Final proposal add successfully', [$responseData, 'value' => $milestones]);
                    return (json_encode($response));
                }
                }
                else{
                        $final_proposal = Final_proposal::create($req->except(['milestones']));
                        $final_proposal_id = $final_proposal->id;
                        if($req->milestones != ' ' ||$req->milestones != null ){
                        $milestones = $milestone->Insert($req->milestones, $req->project_id, $final_proposal_id, $req->price);
                        }
                        if ($milestones == 101) {
                            $del = Final_proposal::where('id',  $final_proposal_id)->delete();
                            $response = Controller::returnResponse(422, 'the milestone percentage  should be multiples of 5', ['value' => $milestones]);
                            return json_encode($response);
                        } elseif ($milestones == 500) {
                            $del = Final_proposal::where('id', $final_proposal_id)->delete();
                            $response = Controller::returnResponse(500, 'something wrong', ["error" => 'add milestone', 'value' => $milestones]);
                            return json_encode($response);
                        }
                        $responseData = array(
                            "Final_proposal_id" => $final_proposal_id,
                        );
                        $response = Controller::returnResponse(200, 'Final proposal add successfully', [$responseData, 'value' => $milestones]);
                        return (json_encode($response));
                }
               
            } catch (Exception $error) {
                $response = Controller::returnResponse(500, 'something wrong', $error);
                return json_encode($response);
            }
        }
    }
    function getProposalByProjectIdAndTeamId(Request $req)
    {

    }

}