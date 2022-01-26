<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agency_target;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

use Exception;
class AgencyTargetsController extends Controller
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
    function addMultiRows($info)
    {
        try {
            Agency_target::insert($info);
            return 200;
        } catch (\Exception $error) {
            return 500;
        }
    }
    function getTargets($id){
        $targets = DB::table('agency_targets')->join('categories', 'agency_targets.category_id', '=', 'categories.id')
        ->where('agency_targets.group_id', '=', $id)->select('categories.id', 'categories.name')->distinct()->get();
        return $targets;

    }
}
