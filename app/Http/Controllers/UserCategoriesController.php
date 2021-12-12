<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\users_category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class UserCategoriesController extends Controller
{
    //add row 
    function Insert($arr, $userId)
    {

        $info['user_id'] = $userId;
        $info['category_id'] = $arr['catId'];
        $info['sub_category_id'] = $arr['subId'];



        users_category::create($info);
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
        users_category::insert($info);
    }

    function getUserCategoriesByUserId($id)
    {
        $allCategory = array();
        try {
            $categories = users_category::where('user_id', $id)->get();
            if (count($categories) > 1) {

                $userCategory = array();
                foreach ($categories as $keyCat => $category) {
                    $userCategory[$category->category_id]['catId'] = $category->category_id;
                    $userCategory[$category->category_id]['name'] = DB::table('categories')->select('name')->where('id', '=', $category->category_id)->get()->first()->name;
                    $userCategory[$category->category_id]['sub'][] = DB::table('sub_categories')->select('id', 'name', 'category_id')->where([['category_id', '=', $category->category_id], ['id', '=', $category->sub_category_id]])->get()->first();
                }

                foreach ($userCategory as $keyUserCat => $valUserCat) {
                    $allCategory[] = $valUserCat;
                }
            }
        } catch (\Exception $error) {

            return $allCategory;
        }
        return $allCategory;
    }
    function updateUserCategories(Request $req)
    {
        

        $del = users_category::where('user_id', $req->user_id)->delete();
        // $cats=$req->categories;
        $cats = json_decode($req->categories);
        $user_id = $req->user_id;
        foreach ($cats as $cat) {
            foreach ($cat->subId as $sub) {

                $id = DB::table('users_categories')->insert(
                    ['user_id' => $user_id, 'category_id' => $cat->catId, 'sub_category_id' => $sub]
                );
            }
        }
        $response = Controller::returnResponse(200, "successful", []);
        return json_encode($response);
    }
}
