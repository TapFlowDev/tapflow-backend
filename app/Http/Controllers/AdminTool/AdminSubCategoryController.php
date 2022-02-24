<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;



class AdminSubCategoryController extends Controller
{
    public function index($category)
    {
        // dd($parentId);
        $subCats = SubCategory::where('category_id', $category)->paginate(10);
        return view('AdminTool.Categories.SubCategories.index', ['category' => $category, 'categories' => $subCats]);
    }
    public function create($category)
    {

        return view('AdminTool.Categories.SubCategories.add', ['categoryId' => $category]);
    }
    public function store(Request $request)
    {
        // $validatedData = $request->validated();
        $category = SubCategory::create($request->except(['_token', 'image']));
        $categoryId = $category->id;
        $parentCategoryId = $category->category_id;
        if ($request->hasFile('image')) {
            $destPath = 'images/categories';
            $ext = $request->file('image')->extension();
            // $image = $request->file('image');
            $imageName = "category-" . $parentCategoryId . "-". $categoryId . "." . $ext;
            // $path = $request->file('image')->storeAs($destPath, $imageName);
            $request->image->move(public_path($destPath), $imageName);
            // $path = $request->file('image')->storeAs($imageName);
            SubCategory::where('id', $categoryId)->update(array('image' => $imageName));
        }
        if ($request->hasFile('image_2')) {
            $destPath = 'images/categories';
            $ext = $request->file('image_2')->extension();
            // $image = $request->file('image');
            $imageName = "category-2-" . $parentCategoryId . "-". $categoryId . "." . $ext;
            // $path = $request->file('image')->storeAs($destPath, $imageName);
            $request->image_2->move(public_path($destPath), $imageName);
            // $path = $request->file('image')->storeAs($imageName);
            SubCategory::where('id', $categoryId)->update(array('image_2' => $imageName));
        }
        return redirect('/AdminTool/categories/'.$request->category_id.'/subCategories');
    }
    public function edit($id)
    {
        // dd(SubCategory::find($id));
        $subCat = SubCategory::find($id);
        return view('AdminTool.Categories.SubCategories.edit', ['subCategory' => $subCat, 'categoryId' => $subCat->category_id]);
    }
    public function update(Request $request, $id)
    {
        // dd($request);
        $category = SubCategory::findOrfail($id);
        $category->update($request->except(['_token', 'category_id', 'image']));
        $categoryId = $category->id;
        $parentCategoryId = $category->category_id;
        if ($request->hasFile('image')) {
            $destPath = 'images/categories';
            $ext = $request->file('image')->extension();
            // $image = $request->file('image');
            $imageName = "category-" . $parentCategoryId . "-". $categoryId . "." . $ext;
            // $path = $request->file('image')->storeAs($destPath, $imageName);
            $request->image->move(public_path($destPath), $imageName);
            // $path = $request->file('image')->storeAs($imageName);
            SubCategory::where('id', $categoryId)->update(array('image' => $imageName));
        }
        if ($request->hasFile('image_2')) {
            $destPath = 'images/categories';
            $ext = $request->file('image_2')->extension();
            // $image = $request->file('image');
            $imageName = "category-2-" . $parentCategoryId . "-". $categoryId . "." . $ext;
            // $path = $request->file('image')->storeAs($destPath, $imageName);
            $request->image_2->move(public_path($destPath), $imageName);
            // $path = $request->file('image')->storeAs($imageName);
            SubCategory::where('id', $categoryId)->update(array('image_2' => $imageName));
        }
        return redirect('/AdminTool/categories/'.$request->category_id.'/subCategories');
    }
    public function destroy($id)
    {
        SubCategory::destroy($id);
        return back();
    }
    public function show($id)
    {
        dd('hi');
    }
}
