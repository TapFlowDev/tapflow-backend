<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Exception;


class CategoriesController extends Controller
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
    public function getCategories(Request $req)
    {

        $categories = Category::all();
        try {
            foreach ($categories as $key => &$value) {
                $value->subs = $this->getSubCategoriesByParent($value->id);
                $value->image = asset('images/categories/' . $value->image);
            }
            // $categories = (array)$categories;
            $response = array(
                "data" => array(
                "message" => "user information added successfully",
                "status" => "200",
                "data" => $categories,
            ));
            return json_encode($response);
        } catch (\Exception $error) {

            $response = array("data" => array(
                "message" => "There IS Error Occurred",
                "status" => "500",
                "error" => $error,
            ));

            return (json_encode($response));
        }
    }
    function getSubCategoriesByParent($category_id)
    {
        return SubCategory::where('category_id', $category_id)->get();
    }
}
