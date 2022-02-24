<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
// use App\Models\SubCategory;
// use Illuminate\Support\Facades\DB;


class AdminCategoriesController extends Controller
{
    public function index($categoryType)
    {
        return view('AdminTool.Categories.index', ['categories' => Category::where('type', $categoryType)->orderBy('id', 'asc')->paginate(20), 'categoryType' => $categoryType]);
    }
    //add row 
    function Insert(Request $req)
    {
    }
    //delete row according to row id
    public function create($categoryType)
    {
        //
        return view('AdminTool.Categories.add', ['type' => $categoryType]);
    }
    public function store(Request $request)
    {
        // $validatedData = $request->validated();


        $category = Category::create($request->except(['_token', 'image', 'image_2']));

        $categoryId = $category->id;
        if ($request->hasFile('image')) {
            $destPath = 'images/categories';
            $ext = $request->file('image')->extension();
            // $image = $request->file('image');
            $imageName = "category-" . $categoryId . "." . $ext;
            // $path = $request->file('image')->storeAs($destPath, $imageName);
            $request->image->move(public_path($destPath), $imageName);
            // $path = $request->file('image')->storeAs($imageName);
            Category::where('id', $categoryId)->update(array('image' => $imageName));
        }
        if ($request->hasFile('image_2')) {
            $destPath = 'images/categories';
            $ext = $request->file('image_2')->extension();
            // $image = $request->file('image');
            $imageName = "category-2-" . $categoryId . "." . $ext;
            // $path = $request->file('image')->storeAs($destPath, $imageName);
            $request->image_2->move(public_path($destPath), $imageName);
            // $path = $request->file('image')->storeAs($imageName);
            Category::where('id', $categoryId)->update(array('image_2' => $imageName));
        }
        return redirect('/AdminTool/categoryTypes/' . $request->type . '/categories');
    }

    public function edit($id)
    {
        // dd(User::find($id));
        $category = Category::find($id);
        return view('AdminTool.Categories.edit', ['category' => $category, 'type' => $category->type]);
    }
    public function update(Request $request, $id)
    {
        $categoryId = $id;
        $category = Category::findOrfail($id);
        $category->update($request->except(['_token', 'image', 'image_2']));
        if ($request->hasFile('image')) {
            $destPath = 'images/categories';
            $ext = $request->file('image')->extension();
            // $image = $request->file('image');
            $imageName = "category-" . $categoryId . "." . $ext;
            // $path = $request->file('image')->storeAs($destPath, $imageName);
            $request->image->move(public_path($destPath), $imageName);
            // $path = $request->file('image')->storeAs($imageName);
            Category::where('id', $categoryId)->update(array('image' => $imageName));
        }
        if ($request->hasFile('image_2')) {
            $destPath = 'images/categories';
            $ext = $request->file('image_2')->extension();
            // $image = $request->file('image');
            $imageName = "category-2-" . $categoryId . "." . $ext;
            // $path = $request->file('image')->storeAs($destPath, $imageName);
            $request->image_2->move(public_path($destPath), $imageName);
            // $path = $request->file('image')->storeAs($imageName);
            Category::where('id', $categoryId)->update(array('image_2' => $imageName));
        }
        return redirect('/AdminTool/categoryTypes/' . $request->type . '/categories');
    }

    public function destroy($id)
    {
        Category::destroy($id);
        return back();
    }

    // public function subCategories($id)
    // {
    //     $subCats = DB::select('select * from sub_categories where category_id = ?', [$id]);
    //     dd($subCats);
    //     return $subCats;
    // }
}
