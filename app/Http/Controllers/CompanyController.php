<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    //add row 
    function Insert($arr)
    {
        return Company::create($arr);
    }
    //update row according to row id
    function Update($id)
    {
    }
    //delete row according to row id
    function Delete($id)
    {
    }

    function updateFiles($groupId, $imageName, $filedName)
    {
        Company::where('group_id', $groupId)->update(array($filedName => $imageName));
    }
}
