<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\projects_category;
use Exception;
use Illuminate\Support\Facades\DB;

class ProjectCategoriesController extends Controller
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
            projects_category::insert($info);
            return 200;
        } catch (Exception $error) {
            return 500;
        }
    }

    function updateProjectCategories(Request $req)
    {
        $project_id = $req->project_id;
        $delete = projects_category::where("project_id", $project_id)->delete();
        $cats = json_decode($req->categories);

        foreach ($cats as $key => $category) {

            $categoryArr = array();
            foreach ($category->subId as $subkey => $subcat) {

                $categoryArr[$subkey]['project_id'] = $project_id;
                $categoryArr[$subkey]['category_id'] = $category->catId;
                $categoryArr[$subkey]['sub_category_id'] = $subcat;
            }
            $this->addMultiRows($categoryArr);
        }
        $response = Controller::returnResponse(200, "successful", []);
        return json_encode($response);
    }
    function getProjectCategories($id)
    {
        $allCategory = array();
        $categories =  projects_category::where('project_id', $id)->get();
        if (count($categories) > 0) {
            $project_categories = array();
            foreach ($categories as  $category) {
                $categoryData = Category::find($category->category_id);
                if ($categoryData != '') {
                    $project_categories[$category->category_id]['id'] = $category->category_id;
                    $project_categories[$category->category_id]['name'] = DB::table('categories')
                        ->select('name')->where('id', '=', $category->category_id)->first()->name;
                    $img = DB::table('categories')->select('image')
                        ->where('id', '=', $category->category_id)->first()->image;
                    if ($img != "") {
                        $project_categories[$category->category_id]['image'] = asset('images/categories/' . DB::table('categories')->select('image')
                            ->where('id', '=', $category->category_id)->first()->image);
                    } else {
                        $project_categories[$category->category_id]['image'] = "Null";
                    }
                    // $sub_image = DB::table('sub_categories')
                    //     ->select('image')
                    //     ->where([['category_id', '=', $category->category_id], ['id', '=', $category->sub_category_id]])->first();
                    // if ($sub_image != "") {

                    //     $project_categories[$category->category_id]['subs'][] = DB::table('sub_categories')
                    //         ->select('category_id', 'id', 'name', "image")
                    //         ->where([['category_id', '=', $category->category_id], ['id', '=', $category->sub_category_id]])->first();
                    // } else {

                    //     $project_categories[$category->category_id]['subs'][] = DB::table('sub_categories')
                    //         ->select('category_id', 'id', 'name')
                    //         ->where([['category_id', '=', $category->category_id], ['id', '=', $category->sub_category_id]])->first();
                    // }
                }
            }
            // foreach ($project_categories as $val) {

            //     $allCategory[] = $val;
            //     $subs_length = count($val['subs']);
            //     for ($i = 0; $i < $subs_length; $i++) {
            //         if (isset($val['subs'][$i]->image)) {
            //             $val['subs'][$i]->image = asset('images/categories/' . $val['subs'][$i]->image);
            //         } else {
            //             $val['subs'][$i]->image = "Null";
            //         }
            //     }
            // }
        }
        return $allCategory;
    }
}
