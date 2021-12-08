<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\groups_category;
use Illuminate\Support\Facades\Validator;
use PHPUnit\TextUI\XmlConfiguration\Groups;
use Illuminate\Support\Facades\DB;

class GroupCategoriesController extends Controller
{
    //add row 
    function Insert($catId, $subId, $groupId)
    {

        $info['group_id'] = $groupId;
        $info['category_id'] = $catId;
        $info['sub_category_id'] = $subId;
        dd($info);
        groups_category::firstOrCreate($info);
    }
    //update row according to row id
    function Update($id)
    {
    }
    //delete row according to row id
    function Delete($id)
    {
    }

    function addMultiRows($info){
        groups_category::insert($info);

    }

    function updateTeamCategories(Request $req)
    {
        $rules=array(
            'group_id'=>"required|exists:groups,id",
            'categories'=>"required",
            
        );
        $validator=Validator::make($req->all(),$rules);
        if($validator->fails())
        {
            $response=Controller::returnResponse(101,'Validation error',$validator->errors());
            return json_encode($response);
        }
        else
        {   
            $group_id=$req->group_id;
            
            $delete=groups_category::where("group_id",$group_id)->delete();
            if(isset($req->local))
            {
              
               foreach($req->categories as $c)
               {
                   foreach($c['subId'] as $s)
                   {
                    $arr=array(
                        'group_id'=>$group_id,
                        'category_id'=>$c['catId'],
                        'sub_category_id'=>$s

                    );
                    $this->addMultiRows($arr);
                   }
               }
            }
            else{
                $cats = json_decode($req->categories);
                if (isset($cats)) {
                    foreach ($cats as $key => $value) {
                        $categoryArr = array();
                        foreach ($value->subCat as $keySub => $subValue) {
                            $categoryArr[$keySub]['group_id'] = $req->user_id;
                            $categoryArr[$keySub]['category_id'] = $value->catId;
                            $categoryArr[$keySub]['sub_category_id'] = $subValue;
                        }
                        $this->addMultiRows($categoryArr);
                    }
                }
        }
            $response=Controller::returnResponse(200,"successful",[]);
            return json_encode($response);
        }

    }
    function updateGroupCategory(Request $req)
    {
       
        $del=groups_category::where('group_id',$req->group_id)->delete();
        // $cats=$req->categories;
        $cats = json_decode($req->categories);
        $group_id=$req->group_id;
        foreach($cats as $cat){
        foreach($cat->subId as $sub){
           
        $id = DB::table('groups_categories')->insert(
            ['group_id' =>$group_id , 'category_id' => $cat->catId,'sub_category_id'=>$sub]
        );
    }}
        $response=Controller::returnResponse(200,"successful",[]);
        return json_encode($response);
       
    }
    function getTeamCategories($id)
    {
        // $cats=groups_category::select('category_id','sub_category_id')->where('group_id',$id)->get();
        $cats=DB::table('groups_categories')
        ->leftJoin('categories', 'groups_categories.category_id', '=', 'categories.id')
        ->leftJoin('sub_categories', 'groups_categories.sub_category_id', '=', 'sub_categories.id')
        ->where('groups_categories.group_id', $id)->select('categories.id','categories.name','categories.image','sub_categories.id','sub_categories.name','sub_categories.image')
        ->get();
        
        return $cats;
    }
}
