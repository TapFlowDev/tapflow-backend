<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CategoriesController;
use Illuminate\Http\Request;
use App\Models\groups_category;
use Illuminate\Support\Facades\Validator;
use PHPUnit\TextUI\XmlConfiguration\Groups;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\SubCategory;

use function GuzzleHttp\Promise\all;

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

    function addMultiRows($info)
    {
        groups_category::insert($info);
    }

    function updateTeamCategories(Request $req)
    {
        
        // $rules = array(
        //     'group_id' => "required|exists:groups,id",
        //     'categories' => "required",

        // );
        // $validator = Validator::make($req->all(), $rules);
        // if ($validator->fails()) {
        //     $response = Controller::returnResponse(101, 'Validation error', $validator->errors());
        //     return json_encode($response);
        // } else {
            // $group_id = $req->group_id;

            $delete = groups_category::where("group_id", $req->group_id)->delete();
            // if (isset($req->local)) {

            //     foreach ($req->categories as $c) {
            //         foreach ($c['subId'] as $s) {
            //             $arr = array(
            //                 'group_id' => $group_id,
            //                 'category_id' => $c['catId'],
            //                 'sub_category_id' => $s

            //             );
            //             $this->addMultiRows($arr);
            //         }
            //     }
            // } else {
                $cats = json_decode($req->categories);
               
                // if (isset($req->categories)) {
                  foreach($cats as$key => $category)
                  {
                    $categoryArr = array();
                    foreach($category->subId as $subkey=>$subcat)
                    {
                        $categoryArr[$subkey]['group_id']=$req->group_id;
                        $categoryArr[$subkey]['category_id']=$category->catId;
                        $categoryArr[$subkey]['sub_category_id']=$subcat;
                    }
                    $this->addMultiRows($categoryArr);
                  }
                //     foreach ($req->categories as $key => $value) {

                //         $categoryArr = array();
                //         foreach ($value->subId as $keySub => $subValue) {
                //             // $categoryArr[$keySub]['group_id'] = $req->group_id;
                //             // $categoryArr[$keySub]['category_id'] = $value->catId;
                //             // $categoryArr[$keySub]['sub_category_id'] = $subValue;
                //             DB::table('groups_categories')->insert([
                //                 'group_id' => $req->group_id,
                //                 'category_id' => $value->catId,
                //                 'sub_category_id' =>$subValue,
                //             ]);
                //         }
                       
                    // }
                 
                // }
            // }
            $response = Controller::returnResponse(200, "successful", []);
            return json_encode($response);
        // }
    }
    function updateGroupCategory(Request $req)
    {

        $del = groups_category::where('group_id', $req->group_id)->delete();
        // $cats=$req->categories;
        $cats = json_decode($req->categories);
        $group_id = $req->group_id;
        foreach ($cats as $cat) {
            foreach ($cat->subId as $sub) {

                $id = DB::table('groups_categories')->insert(
                    ['group_id' => $group_id, 'category_id' => $cat->catId, 'sub_category_id' => $sub]
                );
            }
        }
        $response = Controller::returnResponse(200, "successful", []);
        return json_encode($response);
    }
    function getTeamCategories($id)
    {
        $allCategory = array();
        $categories = groups_category::where('group_id', $id)->get();
        if (count($categories) > 1) {
            $team_categories = array();
            foreach ($categories as  $category) {
                $team_categories[$category->category_id]['category_id'] = $category->category_id;
                $team_categories[$category->category_id]['name'] = DB::table('categories')
                ->select('name')->where('id', '=', $category->category_id)->first()->name;

                $img=DB::table('categories')->select('image')
                ->where('id', '=', $category->category_id)->first()->image;
               
                if($img !=""){
                $team_categories[$category->category_id]['image'] = asset('images/categories/'.DB::table('categories')->select('image')
                ->where('id', '=', $category->category_id)->first()->image);
                }
                else{
                    $team_categories[$category->category_id]['image']="Null";
                }
                $sub_image=DB::table('sub_categories')
                ->select('image')
                ->where([['category_id', '=', $category->category_id],['id', '=', $category->sub_category_id]])->first();
                
                if($sub_image !=""){

                    $team_categories[$category->category_id]['subs'][] = DB::table('sub_categories')
                    ->select('id', 'name',"image")
                    ->where([['category_id', '=', $category->category_id],['id', '=', $category->sub_category_id]])->first();
                 
                    // $team_categories[$category->category_id]['subs'][]=$sub;
                }else{
                    $sub_image="Null";
                    $team_categories[$category->category_id]['subs'][] = DB::table('sub_categories')
                    ->select('id', 'name')
                    ->where([['category_id', '=', $category->category_id],['id', '=', $category->sub_category_id]])->first();
                }
                // $team_categories[$category->category_id]['subs'][] = DB::table('sub_categories')
                // ->select('id', 'name','image')
                // ->where([['category_id', '=', $category->category_id],['id', '=', $category->sub_category_id]])->first();
                // $team_categories[$category->category_id]['sub'][] = asset('images/categories/'.DB::table('sub_categories')->select('image',"id","name")
                // ->where([['category_id', '=', $category->category_id], ['id', '=', $category->sub_category_id]])->first()->image);
                 

            }
            foreach ($team_categories as $val) {
          
                $allCategory[] = $val;
                $subs_length=count($val['subs']);
                for($i=0;$i<$subs_length;$i++){
                    if(isset($val['subs'][$i]['image'])){
                $val['subs'][$i]->image=asset('images/categories/'.$val['subs'][$i]->image);}
                // $val['subs'][$i]->image=asset('images/categories/'.$val['subs'][$i]->image);
                }
            }   

            // foreach($team_categories as $tc)
            // {
            //     $alldata[]=$tc;
            //     // $sl=count($tc['subs']);
            //     // for($i=0; $i<$sl;$i++)
            //     // {   $subimg=$tc['subs'][$i]->image;

            //     //    if(isset($subimg) && $subimg!= null)
            //     //    {
            //     //         $tc['subs'][$i]->image=asset('images/categories/'.$tc['subs'][$i]->image);
            //     //    }
            //     //    else{
            //     //     $tc['subs'][$i]->image="NULL";
            //     //     }
            //     // }
            // }
            
          
        }
        return $allCategory;
    }
}
