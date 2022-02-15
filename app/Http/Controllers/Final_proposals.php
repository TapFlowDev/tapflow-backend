<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Final_proposal;
use Exception;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Finally_;

use function PHPUnit\Framework\isEmpty;

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
            "milestones" => "required"
        );
        $validators = Validator::make($req->all(), $rules);
        if ($validators->fails()) {
            $responseData = $validators->errors();
            $response = Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        } else {
            try {
                $ifExist=$this->checkIfProposalExists( $req->project_id,$req->team_id);
                 
                if ($ifExist['exist'] =='0') {
                    $price = $req->price;
                $dividable = fmod($price, 5);
                if ($dividable == 0) {
                    $final_proposal = Final_proposal::create($req->except(['milestones']));
                    $final_proposal_id = $final_proposal->id;
                    $milestones = $milestone->Insert($req->milestones, $req->project_id, $final_proposal_id, $req->price);

                    if ($milestones['code'] == 101) {
                        $del = Final_proposal::where('id',  $final_proposal_id)->delete();
                        $response = Controller::returnResponse(422, 'the milestone percentage  should be multiples of 5', ['value' => $milestones]);
                        return json_encode($response);
                    } elseif ($milestones['code'] == 500) {
                        $del = Final_proposal::where('id', $final_proposal_id)->delete();
                        $response = Controller::returnResponse(500, 'something wrong', ["error" => 'add milestone', 'value' => $milestones]);
                        return json_encode($response);
                    }
                    // elseif ($milestones == 102) {
                    //     $del = Final_proposal::where('id', $final_proposal_id)->delete();
                    //     $response = Controller::returnResponse(422, 'the milestone price  should be multiples of 5', ['value' => $milestones]);
                    //     return json_encode($response);
                    // }
                    $responseData = array(
                        "Final_proposal_id" => $final_proposal_id,
                    );
                    $response = Controller::returnResponse(200, 'Final proposal add successfully', $responseData);
                    return (json_encode($response));
                    
                } else {
                    $response = Controller::returnResponse(422, 'the price should be multiples of 5', []);
                    return json_encode($response);
                }
                } else {
                   $proposal=json_decode( $this->getProposalDetailsById($ifExist['final_proposal_id']));
                   $status=$proposal->status;
                    if($status == -1)
                    {
                       $a= $this->updateFinalProposalInternal($req,$ifExist['final_proposal_id']);
                       return $a;;
                    }
                    else{
                    $response = Controller::returnResponse(422, 'You already have proposal ',["final_proposal_id"=>$proposal]);
                    return json_encode($response);}
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
        $final_proposal = Final_proposal::select('id', 'team_id', 'project_id', 'price', 'days', 'description','status')
            ->where('id', $id)->first();
        $milestones = $milestone->getMilestoneByProposalId($id);
        $final_proposal->milestones = $milestones;
        return json_encode($final_proposal);
    }
    //i need to delete every milestone and tasks belong to the same proposal 
    function updateFinalProposal(Request $req)
    
    {   
        
        $milestoneObj = new Milestones;
        $milestones=$req->milestones;
        $final_proposal_id = $req->final_proposal_id;
        

        $project_id=Final_proposal::where('id', $final_proposal_id)->select('project_id')->first();  
       
        Final_proposal::where('id', $final_proposal_id)
        ->update(["title"=>$req->title , "price"=>$req->price ,
        "description"=>$req->description,"days"=>$req->days,"starting_date"=>$req->starting_date]);
        $milestoneObj-> deleteMilestonesByProposalId( $req->final_proposal_id);
       
        // dd($req->milestones);
        
        if( $milestones == null )
        {
            $response = Controller::returnResponse(200, 'final proposal updated', ["milestones"=>'empty']);
                return json_encode($response);
        }
        else
        {

            $milestones = $milestoneObj->Insert($req->milestones, $project_id->project_id, $final_proposal_id, $req->price);
            $msg="add";
            if ($milestones == 101) {
                $del = Final_proposal::where('id',  $final_proposal_id)->delete();
                $response = Controller::returnResponse(422, 'the milestone percentage  should be multiples of 5', ['value' => $milestones]);
                return json_encode($response);
            } elseif ($milestones == 500) {
                $del = Final_proposal::where('id', $final_proposal_id)->delete();
                $response = Controller::returnResponse(500, 'something wrong', ["error" => 'add milestone', 'value' => $milestones]);
                return json_encode($response);
            }
        }
       
        $response = Controller::returnResponse(200, 'final proposal updated', []);
                return json_encode($response);
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
        }
      
         else {
            try {
                $final_prop = DB::table('final_proposals')
                ->select('id', 'team_id', 'project_id', 'price', 'days', 'description')
                ->where('team_id', '=', $req->team_id)
                ->where('project_id', '=', $req->project_id)
                ->first();
                if ($final_prop == null) {
                    $response = Controller::returnResponse(422, 'You did not send any final proposals',[]);
                    return json_encode($response);
                } else {
                    $price = $req->price;
                    if ($price != null) {
                        $dividable = fmod($price, 5);
                        if ($dividable == 0) {
    
                            $final_proposal = Final_proposal::create($req->except(['milestones']))+['status'=>'-1'];
                            $final_proposal_id = $final_proposal->id;
                            if ($req->milestones != ' ' || $req->milestones != null) {
                                $milestones = $milestone->Insert($req->milestones, $req->project_id, $final_proposal_id, $req->price);
                            }
                            if ($milestones['code'] == 101) {
                                $del = Final_proposal::where('id',  $final_proposal_id)->delete();
                                $response = Controller::returnResponse(422, 'the milestone percentage  should be multiples of 5', ['value' => $milestones]);
                                return json_encode($response);
                            } elseif ($milestones['code'] == 500) {
                                $del = Final_proposal::where('id', $final_proposal_id)->delete();
                                $response = Controller::returnResponse(500, 'something wrong', ["error" => 'add milestone', 'value' => $milestones]);
                                return json_encode($response);
                            }
                            $responseData = array(
                                "Final_proposal_id" => $final_proposal_id,
                            );
                            $response = Controller::returnResponse(200, 'Final proposal saved successfully', [$responseData, 'value' => $milestones]);
                            return (json_encode($response));
                        }
                    } else {
                        $final_proposal = Final_proposal::create($req->except(['milestones']));
                        $final_proposal_id = $final_proposal->id;
                        if ($req->milestones != ' ' || $req->milestones != null) {
                            $milestones = $milestone->Insert($req->milestones, $req->project_id, $final_proposal_id, $req->price);
                        }
                        if ($milestones['code']== 101) {
                            $del = Final_proposal::where('id',  $final_proposal_id)->delete();
                            $response = Controller::returnResponse(422, 'the milestone percentage  should be multiples of 5', ['value' => $milestones]);
                            return json_encode($response);
                        } elseif ($milestones['code'] == 500) {
                            $del = Final_proposal::where('id', $final_proposal_id)->delete();
                            $response = Controller::returnResponse(500, 'something wrong', ["error" => 'save milestone', 'value' => $milestones]);
                            return json_encode($response);
                        }
                        $responseData = array(
                            "Final_proposal_id" => $final_proposal_id,
                        );
                        $response = Controller::returnResponse(200, 'Final proposal save successfully', [$responseData, 'value' => $milestones]);
                        return (json_encode($response));
                    }
                }
               
            } catch (Exception $error) {
                $response = Controller::returnResponse(500, 'something wrong', $error->getMessage());
                return json_encode($response);
            }
        }
    }
    function getProposalByProjectIdAndTeamId(Request $req)
    {
        $userData = $req->user();
        $rules = array(
            "project_id" => "required|exists:projects,id",
        );
        $validators = Validator::make($req->all(), $rules);
        if ($validators->fails()) {
            $responseData = $validators->errors();
            $response = Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        } else {
            try {

                $GroupControllerObj = new GroupController;
                $team_id = $GroupControllerObj->getGroupIdByUserId($userData->id);
                $milestone = new Milestones;
                $ifExist=$this->checkIfProposalExists( $req->project_id,$team_id);
                if ($ifExist['exist'] ==0) {
                    $response = Controller::returnResponse(422, 'You did not send any final proposals',[]);
                    return json_encode($response);
                } else {
                    $final_proposal_id = $ifExist['final_proposal_id'];
                    $milestones = $milestone->getMilestoneByProposalId($final_proposal_id);
                    $final_proposal = DB::table('final_proposals')
                    ->select('id', 'team_id', 'project_id', 'price', 'days', 'description')
                    ->where('id', '=', $final_proposal_id)
                    ->first();
                    $final_proposal->milestones = $milestones;
                    return json_encode($final_proposal);
                }
            } catch (Exception $error) {
                $response = Controller::returnResponse(500, 'you are trying to get proposal does not belong to you', $error->getMessage());
                return json_encode($response);
            }
        }
    }
    //get proposal info with out milestones
    function getProposalById($id)
    {
        $final_proposal = Final_proposal::select('id', 'team_id', 'project_id', 'price', 'days', 'description')
            ->where('id', $id)->first();
        return $final_proposal;
    } function checkIfProposalExists($project_id,$team_id)
    {
        $final_proposal_id = DB::table('final_proposals')
                    ->select('id')
                    ->where('team_id', '=', $team_id)
                    ->where('project_id', '=', $project_id)
                    ->first();
                if ($final_proposal_id == null) {
                   return ['exist'=>0];
                } else {
                     return ['exist'=>1 ,"final_proposal_id"=>$final_proposal_id->id];
                     }
    }
    function updateFinalProposalInternal($req,$final_proposal_id)
    
    {   
        $milestoneObj = new Milestones;
        $milestones=$req->milestones;
        $project_id=Final_proposal::where('id', $final_proposal_id)->select('project_id')->first();  
        Final_proposal::where('id', $final_proposal_id)
        ->update(["title"=>$req->title , "price"=>$req->price ,
        "description"=>$req->description,"days"=>$req->days,"starting_date"=>$req->starting_date]);
        $milestoneObj-> deleteMilestonesByProposalId($final_proposal_id);
        if( $milestones == null )
        {
            $response = Controller::returnResponse(200, 'final proposal '.$final_proposal_id.' updated', ["milestones"=>'empty']);
                return json_encode($response);
        }
        else
        {
            $milestones = $milestoneObj->Insert($req->milestones, $project_id->project_id, $final_proposal_id, $req->price);
            $msg="add";
            if ($milestones['code'] == 101) {
                $del = Final_proposal::where('id',  $final_proposal_id)->delete();
                $response = Controller::returnResponse(422, 'the milestone percentage  should be multiples of 5', ['value' => $milestones]);
                return ["code"=>'101','msg'=>'Milestone Validation error'];
            } elseif ($milestones['code'] == 500) {
                $del = Final_proposal::where('id', $final_proposal_id)->delete();
                $response = Controller::returnResponse(500, 'something wrong', ["error" => 'add milestone', 'value' => $milestones]);
                return ["code"=>'500','msg'=>$milestones['msg']];
            }
        }
        $response = Controller::returnResponse(200, 'final proposal '.$final_proposal_id.' updated', []);
        return ["code"=>'200','msg'=>'success'];
    }
    
    //get all the final proposals for a specific project
    function getProjectProposalsById($id)
    {
        $final_proposal_ids=Final_proposal::where('project_id',$id)->select('id')->get();
        $proposals=array();
        // dd($final_proposal_ids);
        foreach($final_proposal_ids as $f_id)
        {
            // $limit = 4;
            // $page = ($offset - 1) * $limit;
            $proposal=DB:: table('final_proposals')
            ->select('id', 'team_id', 'project_id', 'price', 'days', 'description','status')
            ->where('id', $id)
            ->first();
            array_push($proposals,json_decode($proposal));
        }
        return ($proposals);
    }
}
