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
        $rules = array(
            'group_id' => "required|exists:groups,id",
            'categories' => "required",

        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $response = Controller::returnResponse(101, 'Validation error', $validator->errors());
            return json_encode($response);
        } else {
            $group_id = $req->group_id;

            $delete = groups_category::where("group_id", $group_id)->delete();
            if (isset($req->local)) {

                foreach ($req->categories as $c) {
                    foreach ($c['subId'] as $s) {
                        $arr = array(
                            'group_id' => $group_id,
                            'category_id' => $c['catId'],
                            'sub_category_id' => $s

                        );
                        $this->addMultiRows($arr);
                    }
                }
            } else {
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
            $response = Controller::returnResponse(200, "successful", []);
            return json_encode($response);
        }
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
        $data2 = DB::table('groups_categories')
            ->Join("sub_categories", "groups_categories.sub_category_id", '=', "sub_categories.id")
            ->where("groups_categories.group_id", "=", $id)->select("sub_categories.id", "sub_categories.name", "sub_categories.image", "groups_categories.category_id")
            ->get();
        $ids = array();
        $all = array();
       
        foreach ($data2 as $value) {
            if (!(in_array($value->category_id, $ids))) {
                array_push($ids, $value->category_id);
                $main = DB::table('categories')
                    ->select("categories.id", "categories.name", "categories.image")
                    ->where("categories.id", "=", $value->category_id)
                    ->first();
                $info = array(
                    "category_id" => $main->id,
                    "category_name" => $main->name,
                    "category_image" => $main->image,
                    "subs" => array(
                        "sub_category_id" => $value->id,
                        "sub_category_name" => $value->name,
                        "sub_category_image" => $value->image,
                    )
                );
                array_push($all,$info);
            } 
            else 
            {
                $index=array_search($value->category_id,$ids);
                $sub=array(
                    "sub_category_id" => $value->id,
                    "sub_category_name" => $value->name,
                    "sub_category_image" => $value->image,
                );
                array_push($all[$index]['subs'],$sub);
            }
        }
        return $all;
    }
}
