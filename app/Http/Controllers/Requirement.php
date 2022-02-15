<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Requirement as requirementModel ;
use Illuminate\Http\Request;
/*
  $table->integer('id')->autoIncrement();
            $table->integer('project_id');
            $table->integer('user_id');
            $table->integer('milestone');
            $table->string('name','255');
            $table->text('description'); 
*/
class Requirement extends Controller
{
    //add row 
    function Insert($data,$project_id,$user_id)
    {
      
      foreach($data as $requirement)

      {
        $arr=array(
          'project_id'=>$project_id,
          'user_id'=>$user_id,
          'description'=>$requirement,
        );
        requirementModel::create($arr);
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
