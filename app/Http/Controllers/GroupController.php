<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Company;
use App\Models\Team;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Rate;
use App\Models\groups_category;
use COM;
use Exception;
use Illuminate\Support\Arr;

class GroupController extends Controller
{   
    
    //add row 
    function add_group(Request $req)
    
    {   
        $group_info=array();
        // $group_categories;
        $group_cats=array();
        // $group_subcategories=array();
     
        
            array_push($group_info,
            $req['name'],$req["admin_id"],
            $req['bio'],$req["attachment"],
            $req['logo'],$req['link'],
            $req['country']
    );
      $group_categories=$req['category'];
    //   $group_subcategories=$group_categories["sub_cat"];
        // $i=0;
        // foreach($group_categories as $cat)
        // {   
            
        //     array_push($group_cats,$cat['cat_id']);
        //     $limit=count($cat['sub_cat']);
        //     if($i < $limit)
        //     {
        //         array_push($group_subcategories,$cat['sub_cat'][$i]['id']);
        //     }
        //     $i=0;
           
        // }
        $cats=array();
        $subs=array();
      
     for($i=0 ; $i< count($group_categories) ;$i++)
     {
        array_push($cats,$group_categories[$i]["cat_id"]);
        for($j=0;$j<count($group_categories[$i]["sub_cat"]);$j++)
        {
            array_push($subs,$group_categories[$i]["sub_cat"][$j]["id"]);
        }
     }

        return($cats); 

    
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
