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
        // $allCategory = array();
        // try {
        //     $categories = users_category::where('user_id', $id)->get();
        //     if (count($categories) > 1) {

        //         $userCategory = array();
        //         foreach ($categories as $keyCat => $category) {
        //             $userCategory[$category->category_id]['id'] = $category->category_id;
        //             $userCategory[$category->category_id]['name'] = DB::table('categories')->select('name')->where('id', '=', $category->category_id)->get()->first()->name;
        //             $userCategory[$category->category_id]['sub'][] = DB::table('sub_categories')->select('id', 'name', 'category_id')->where([['category_id', '=', $category->category_id], ['id', '=', $category->sub_category_id]])->get()->first();
        //         }

        //         foreach ($userCategory as $keyUserCat => $valUserCat) {
        //             $allCategory[] = $valUserCat;
        //         }
        //     }
        // } catch (\Exception $error) {

        //     return $allCategory;
        // }
        // return $allCategory;
        $allCategory = array();
        $categories = users_category::where('user_id', $id)->get();
        if (count($categories) > 1) {
            $user_categories = array();
            foreach ($categories as  $category) {
                $user_categories[$category->category_id]['id'] = $category->category_id;
                $user_categories[$category->category_id]['name'] = DB::table('categories')
                ->select('name')->where('id', '=', $category->category_id)->first()->name;

                $img=DB::table('categories')->select('image')
                ->where('id', '=', $category->category_id)->first()->image;
               
                if($img !=""){
                $user_categories[$category->category_id]['image'] = asset('images/categories/'.DB::table('categories')->select('image')
                ->where('id', '=', $category->category_id)->first()->image);
                }
                else{
                    $user_categories[$category->category_id]['image']="Null";
                }
                $sub_image=DB::table('sub_categories')
                ->select('image')
                ->where([['category_id', '=', $category->category_id],['id', '=', $category->sub_category_id]])->first();
                if($sub_image !=""){

                    $user_categories[$category->category_id]['subs'][] = DB::table('sub_categories')
                    ->select('category_id','id', 'name',"image")
                    ->where([['category_id', '=', $category->category_id],['id', '=', $category->sub_category_id]])->first();
                }else{
                   
                    $user_categories[$category->category_id]['subs'][] = DB::table('sub_categories')
                    ->select('category_id','id', 'name')
                    ->where([['category_id', '=', $category->category_id],['id', '=', $category->sub_category_id]])->first();
                }
            }
            foreach ($user_categories as $val) {
          
                $allCategory[] = $val;
                $subs_length=count($val['subs']);
                for($i=0;$i<$subs_length;$i++){
                    if(isset($val['subs'][$i]->image)){
                $val['subs'][$i]->image=asset('images/categories/'.$val['subs'][$i]->image);}
                else{ $val['subs'][$i]->image="Null";}
               
                }
            }  
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
