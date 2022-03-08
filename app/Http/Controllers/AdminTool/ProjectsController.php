<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProjectCategoriesController;
use App\Http\Controllers\Proposals;
use App\Http\Controllers\AdminTool\TeamsController;
use App\Http\Controllers\AdminTool\EmailController;
use App\Http\Controllers\GroupMembersController;
use App\Http\Controllers\Requirement;
use App\Http\Controllers\UserController;
use App\Models\Category;
use App\Models\Company;
use App\Models\Group;
use App\Models\Group_member;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\proposal;
use App\Models\SubCategory;
use Illuminate\Support\Facades\DB;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = $this->getData(Project::where('status', '<>', -1)->latest()->paginate(20));
        // return $projects;
        return view('AdminTool.Projects.index', ['projects' => $projects]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $req)
    {
        $categories = Category::where('type', 1)->get();
        foreach ($categories as $key => &$value) {
            $value->subs = $this->getSubCategoriesByParent($value->id);
        }
        $company_id = $req->company_id;
        $duration = Category::where('type', 2)->get();
        return view('AdminTool.Projects.add', ['categories' => $categories, 'company_id' => $company_id, 'duration' => $duration]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        $validated = $req->validate([
            "company_id" => "required|exists:groups,id",
            "name" => "required",
            "description" => "required",
            "requirements_description" => "required",
            "min" => "numeric|multiple_of:5|gt:0",
            "max" => "numeric|multiple_of:5|gt:0",
            "days" => "required|exists:categories,id",
        ]);
        // return $req;

        $userObj = new UserController;
        $ProjectCategoriesObj = new ProjectCategoriesController;
        $requirementObj = new Requirement;

        $userGroupInfo = Group_member::where('group_id', '=', $req->company_id)->get()->first();
        $userInfo = $userObj->getUserById($userGroupInfo->user_id);
        $project = Project::create($req->except(['requirements_description', 'categories']) + ["user_id" => $userGroupInfo->user_id, 'budget_type' => 0, 'status' => -1]);
        $project_id = $project->id;
        $reqs = $requirementObj->Insert($req->requirements_description, $project_id, $userGroupInfo->user_id);
        $cats = $req->categories;
        foreach ($cats as $key => $value) {
            $categoryArr = array();
            foreach ($value as $keySub => $subValue) {
                $categoryArr[$keySub]['project_id'] = $project_id;
                $categoryArr[$keySub]['category_id'] = $key;
                $categoryArr[$keySub]['sub_category_id'] = (int)$subValue;
            }
            $add_cat = $ProjectCategoriesObj->addMultiRows($categoryArr);
        }

        return redirect('/AdminTool/dummyCompanies');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $teamObj = new TeamsController;
        $project = $this->getData(Project::where('id', '=', $id)->get())->first();
        //return $project;
        $teams = [];
        if ($project->team_id != '') {
            $teams = $teamObj->getTeamById($project->team_id);
            $status = 1;
        } else {
            $status = 0;
            $teamsIds = proposal::select('team_id')->where('project_id', '=', $id)->distinct()->pluck('team_id');
            foreach ($teamsIds as $teamId) {
                $teamInfo = $teamObj->getTeamById($teamId);
                if ($teamInfo != '') {
                    $teams[] = $teamInfo;
                }
            }
            // return $teams;
        }
        // $allTeams = $teamObj->getAllTeams();
        return view("AdminTool.Projects.show", ['project' => $project, 'status' => $status, 'teams' => $teams]);
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
        $companyObj = new CompaniesController;
        $requirementsObj = new Requirement;
        foreach ($array as $keyP => &$project) {
            $duration = Category::find((int)$project->days);
            $company = Group::find($project->company_id);
            // $company_details = Company::where("group_id", "=", $project->company_id)->get()->first();
            $company_details = $companyObj->getCompanyById($project->company_id);
            if ($project->budget_type < 1) {
                $project->duration = $duration->name;
            } else {
                $project->duration = "unset";
            }
            // if ($company_details->image != "") {
            //     $company_details->image = asset('images/companies/' . $company_details->image);
            // } else {
            //     $company_details->image = asset('images/profile-pic.jpg');
            // }
            $project->company_name = $company->name;
            $project->company_image = $company_details->image;
            $project->categories = $projectCatObj->getProjectCategories($project->id);
            $project->admin_name = $company_details->admin_name;
            $project->admin_id = $company_details->admin_id;
            $project->admin_email = $company_details->admin_email;
            $project->requirments_description = $requirementsObj->getRequirementsByProjectId($project->id)->pluck('description')->toArray();

            // $str = $project->requirements_description;
            // // $requirements_description = json_decode($str, TRUE);

            // return preg_split("/[\s,]+/", $str);
        }
        return $array;
    }

    function sendAgenciesEmail(Request $request, $id)
    {
        // return $request->teamsIds;
        $teamObj = new TeamsController;
        $emailObj = new EmailController;
        $project = $this->getData(Project::where('id', '=', $id)->get())->first();
        $teamsIds = $request->teamsIds;
        //return $project;
        try {
            foreach ($teamsIds as $teamId) {
                $teamInfo = $teamObj->getTeamById($teamId);
                if ($teamInfo != '') {
                    $teams[] = $teamInfo;
                }
            }
            $emailSent = $emailObj->sendEmailToAgencies($teams, $project);
            $request->session()->flash('success', 'email sent successfully');
        } catch (\Exception $error) {
            $request->session()->flash('error', 'email was not sent due to an error');
        }
        return redirect()->back();
    }

    function getSubCategoriesByParent($category_id)
    {
        return SubCategory::where('category_id', $category_id)->get();
    }

    function getProjectRequests(){
        $projects = DB::table('projects')->join('proposals', 'projects.id', '=','proposals.project_id')->where('projects.status', '<>', -1)->count();
        return $projects;
    }

    function recommendProject($id, Request $req)
    {
        $projectCatObj = new ProjectCategoriesController;

        $project = Project::find($id);
        $categories = Category::where('type', 1)->get();

        foreach ($categories as $key => &$value) {
            $value->subs = $this->getSubCategoriesByParent($value->id);
        }
        if ($req->subs != '') {
            $projectCategories = $req->subs;
            $agencies = DB::table('groups_categories')
            ->join('groups', 'groups_categories.group_id', '=', 'groups.id')
            ->select('groups.*')
            ->whereIn('groups_categories.sub_category_id', $projectCategories)
            ->where('groups.status', '=', 1)
            ->distinct()
            ->get();
        } else {
            // $projectCategories = DB::table('projects_categories')->select('sub_category_id')->where('project_id', '=', $id)->pluck('sub_category_id')->toArray();
            $projectCategories = [];
            $agencies =  DB::table('teams')
                ->join('groups', 'teams.group_id', '=', 'groups.id')
                ->select('groups.*', 'teams.*')
                ->where('groups.status', '=', 1)
                ->where('groups.deleted', '=', 0)
                ->get();
        }
        // dd($agencies);   
        return view('AdminTool.Projects.recommendProject', ['categories' => $categories, 'agencies' => $agencies, 'projectCategories' => $projectCategories, 'project' => $project]);

        // return $projects;
    }
  
}
