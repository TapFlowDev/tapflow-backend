<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GroupCategoriesController;
use App\Http\Controllers\AdminTool\ProjectsController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Countries;
use App\Models\User;
use App\Models\Group_member;
use App\Models\Project;
use App\Models\wallet;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = $this->getData(DB::table('companies')
            ->join('groups', 'companies.group_id', '=', 'groups.id')
            ->select('groups.*', 'companies.*')
            ->where('groups.type', '<>', -1)
            ->orderBy('groups.created_at', 'desc')
            ->paginate(20));

        return view('AdminTool.Companies.index', ['users' => $companies]);
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
        $projectObj = new ProjectsController;
        $teams = DB::table('companies')
            ->join('groups', 'companies.group_id', '=', 'groups.id')
            ->select('groups.*', 'companies.*')
            ->where('groups.id', $id)
            ->get();
        $teamsInfo = $this->getData($teams)->first();
        $projects = Project::where('company_id', '=', $teamsInfo->id)->latest()->get();
        // return $projects;
        return view('AdminTool.Companies.show',  ['info' => $teamsInfo, 'projects' => $projects]);
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
    function getCompanyById($id)
    {
        $teams = DB::table('companies')
            ->join('groups', 'companies.group_id', '=', 'groups.id')
            ->select('groups.*', 'companies.*')
            ->where('groups.id', $id)
            ->get();
        $teamsInfo = $this->getData($teams)->first();
        return $teamsInfo;
    }
    private function getData($array)
    {
        $groupCatObj = new GroupCategoriesController;
        foreach ($array as $key => &$group) {
            $admin = Group_member::select('user_id')->where('group_id', $group->group_id)->get()->first();
            $userInfo = User::find($admin->user_id);
            $walletInfo = wallet::where('reference_id', '=', $group->id)->where('type', '=', 1)->get()->first();
            $group->admin_name = $userInfo->first_name . " " . $userInfo->last_name;
            $group->admin_id = $userInfo->id;
            $group->admin_email = $userInfo->email;
            $group->categories = $groupCatObj->getTeamCategories($group->id);
            if ($group->image != "") {
                $group->image = asset('images/companies/' . $group->image);
            } else {
                $group->image = asset('images/profile-pic.jpg');
            }
            $field = Category::find($group->field);
            if ($field != "") {
                $group->field_name = $field->name;
            } else {
                $group->field_name = "Unset";
            }
            $sector = Category::find($group->sector);
            if ($sector != "") {
                $group->sector_name = $sector->name;
            } else {
                $group->sector_name = "Unset";
            }

            $country = Countries::find($group->country);
            if ($country != "") {
                $group->country_name = $country->name;
            } else {
                $group->country_name = "Unset";
            }
            $group->walletId = '';
            if ($walletInfo) {
                // dd($walletInfo->id);
                $group->walletId = $walletInfo->id;
            }

            // $group->sector
        }
        return $array;
    }
}
