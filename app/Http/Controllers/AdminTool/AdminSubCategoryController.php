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
        SubCategory::create($request->except(['_token', 'image']));
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
