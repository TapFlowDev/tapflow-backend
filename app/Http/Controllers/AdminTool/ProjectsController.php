<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProjectCategoriesController;
use App\Models\Category;
use App\Models\Company;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Models\Project;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = $this->getData(Project::latest()->paginate(20));
        // return $projects;
        return view('AdminTool.Projects.index', ['projects' => $projects]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $project = $this->getData(Project::where('id', '=', $id)->get())->first();
        //return $project;
        return view("AdminTool.Projects.show", ['project' => $project]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    function getProjectsByCompanyId($company_id)
    {
        $projects = $this->getData(Project::where('company_id', '=', $company_id)->latest()->get());
        return $projects;
    }
    private function getData($array)
    {
        $projectCatObj = new ProjectCategoriesController;
        foreach ($array as $keyP => &$project) {
            $duration = Category::find((int)$project->days);
            $company = Group::find($project->company_id);
            $company_details = Company::where("group_id", "=", $project->company_id)->get()->first();
            if ($project->budget_type < 1) {
                $project->duration = $duration->name;
            } else {
                $project->duration = "unset";
            }
            if ($company_details->image != "") {
                $company_details->image = asset('images/users/' . $company_details->image);
            } else {
                $company_details->image = asset('images/profile-pic.jpg');
            }
            $project->company_name = $company->name;
            $project->company_image = $company_details->image;
            $project->categories = $projectCatObj->getProjectCategories($project->id);
            // $str = $project->requirements_description;
            // // $requirements_description = json_decode($str, TRUE);

            // return preg_split("/[\s,]+/", $str);
        }
        return $array;
    }
}
