<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectAgencyMatchController extends Controller
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
    // function getMatchByProjectId($projectId){
    //     $matches = DB::table('project_agency_matches')
    //     ->join('groups', 'project_agency_matches.group_id', '=', 'groups.id')
    //     ->select('groups.*')
    //     ->where('project_agency_matches.project_id', '=', $projectId)
    //     ->get();
    //     return $matches;
    // }
    function getMatchCountByProjectId($projectId){
        $matchesCount = DB::table('project_agency_matches')
        ->where('project_id', '=', $projectId)
        ->count();
        return $matchesCount;
    }
}
