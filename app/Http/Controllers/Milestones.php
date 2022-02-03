<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Milestone;
use Exception;
use Illuminate\Foundation\Mix;

use App\Http\Controllers\TasksController;


class Milestones extends Controller
{
    //add row 
    function Insert($data, $project_id, $final_proposal_id, $final_price)

    {

        $Tasks = new TasksController;

        try{
        foreach ($data as $milestone) {
            $arr = array(
                "project_id" => $project_id,
                "final_proposal_id" => $final_proposal_id,
                "name" => $milestone['name'],
                "days" => $milestone['days'],
                "description" => $milestone['description'],
                "percentage" =>$milestone['percentage'],
            );
            $percentage=$milestone['percentage'];
            $dividable =fmod($percentage,5);
            if($dividable == 0)
            {
                $milestone_price=$this->calculatePrice($percentage,$final_price);
                $mp=fmod($milestone_price,5);
               
                if($mp == 0.0)
                {
                    $milestone_info = Milestone::create($arr+["price"=>$milestone_price]);

                    $Tasks->Insert($milestone['tasks'], $milestone_info->id);
                }
                
                else
                {
                    return 101;
                }

            }
            else{ 
                return 101;
            }
          


         
        }
        return 200;
        }catch(Exception $error)
        {
            return 500;
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
    private function calculatePrice($percentage, $final_price)
    {

        $mPrice = $final_price * ($percentage/100);
        return $mPrice;
    }
    private function dividableBy5($number)
    {
        $mod = $number % 5;
        if ($mod == 0) {
            return 1;
        } else {
            return 0;
        }
    }
    function getMilestoneByProposalId($id)
    {
        $Tasks = new TasksController;
        $milestones=Milestone::select('id','project_id','final_proposal_id','name','description','days','percentage','price','status')
        ->where('final_proposal_id',$id)->get();
        $milestones_details=[];
        
        foreach ($milestones as $milestone)
        {
            
           $tasks= $Tasks->getTaskByMilestoneId($milestone->id);
           array_push($milestones_details,array(
            "milestone_id"=>$milestone->id,
            "milestone_name"=>$milestone->name,
            "milestone_description"=>$milestone->description,
            "tasks"=>($tasks),
           
        ));
        }
        return ($milestones_details);
    }
}
