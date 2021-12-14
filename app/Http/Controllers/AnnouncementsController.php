<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement; 

class AnnouncementsController extends Controller
{
    //add row 
    function getAnnouncementsByLimit($offset = 2){
        $data = $this->getDataInfo(Announcement::select('content', 'logo', 'template', 'link')->latest()->offset($offset-1)->limit(2)->get());
        return $data;
        try{
        }catch(\Exception $error){

        }
    }
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
    private function getDataInfo($announcements){
        foreach($announcements as $key => &$announcement){
            $announcement->logo = asset("images/announcements/".$announcement->logo);
        }
        
        return $announcements;
    }
}
