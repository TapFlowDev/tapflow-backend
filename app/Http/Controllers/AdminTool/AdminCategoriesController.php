<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
// use App\Models\SubCategory;
// use Illuminate\Support\Facades\DB;


class AdminCategoriesController extends Controller
{
    public function index()
    {
        return view('AdminTool.Categories.index', ['categories' => Category::paginate(10)]);
    }
    //add row 
    function Insert(Request $req)
    {
    }
    //delete row according to row id
    public function create()
    {
        //
        return view('AdminTool.categories.add');
    }
    public function store(Request $request)
    {
        // $validatedData = $request->validated();


        $category = Category::create($request->except(['_token', 'image']));

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
        return redirect('/AdminTool/categories');
    }

    public function edit($id)
    {
        // dd(User::find($id));
        return view('AdminTool.categories.edit', ['category' => Category::find($id)]);
    }
    public function update(Request $request, $id)
    {
        $categoryId = $id;
        $category = Category::findOrfail($id);
        $category->update($request->except(['_token', 'image']));
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
        return redirect('/AdminTool/categories');
    }

    public function destroy($id)
    {
        Category::destroy($id);
        return redirect('/AdminTool/categories');
    }

    // public function subCategories($id)
    // {
    //     $subCats = DB::select('select * from sub_categories where category_id = ?', [$id]);
    //     dd($subCats);
    //     return $subCats;
    // }
}
