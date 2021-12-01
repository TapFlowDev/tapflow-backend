<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Groups_link;
use Illuminate\Support\Arr;

class GroupsLinksController extends Controller
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
     function get_group_links($id)
    {
        $links=Groups_link::select('link')->where('group_id',$id)->get();  
       
        if(count($links)>0)
        {    $data=array_column($links->toArray(), 'link');
        return($data);
        }
    else{return('null');}
    }
}

