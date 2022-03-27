<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GroupCategoriesController;
use App\Models\Countries;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Group_member;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class TeamsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teams = DB::table('teams')
            ->join('groups', 'teams.group_id', '=', 'groups.id')
            ->select('groups.*', 'teams.*')
            ->where('groups.status', '=', 1)
            ->where('groups.deleted', '=', 0)
            ->orderBy('groups.created_at', 'desc')
            ->paginate(20);
        $teamsInfo = $this->getData($teams);
        // return $teamsInfo;

        return view('AdminTool.Agencies.index', ['users' => $teamsInfo]);
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
        $teams = DB::table('teams')
            ->join('groups', 'teams.group_id', '=', 'groups.id')
            ->select('groups.*', 'teams.*')
            ->where('groups.id', $id)
            ->get();
        $teamsInfo = $this->getData($teams)->first();

        // dd();
        // return $info->first();
        return view('AdminTool.Agencies.show',  ['info' => $teamsInfo]);
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
        $team = DB::table('teams')
            ->join('groups', 'teams.group_id', '=', 'groups.id')
            ->select('groups.*', 'teams.*')
            ->where('groups.id', $id)
            ->get()->first();
        return view('AdminTool.Agencies.edit', ['team' => $team]);
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
        $validated = $request->validate([
            'minPerHour' => 'required|numeric|gte:0',
            'maxPerHour' => 'required|numeric|gte:0',
            'min_work_hour' => 'required|numeric|gte:0',
            'max_work_hour' => 'required|numeric|gte:0',
            'lead_time' => 'required|numeric'
        ]);
        $team = Team::where('group_id', '=', $id)->update([
            "minPerHour" => $request->minPerHour,
            "maxPerHour" => $request->maxPerHour,
            "min_work_hour" => $request->min_work_hour,
            "max_work_hour" => $request->max_work_hour,
            "lead_time" => $request->lead_time
        ]);
        return redirect('/AdminTool/agencies');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $teamObj = new Group;
        $team = $teamObj::find($id);
        $team->status = 0;
        $team->deleted = 1;
        $team->save();
        Group_member::where('group_id', '=', $id)->delete();
        return redirect('/AdminTool/agencies');
    }
    function getUnverifiedAgencies()
    {
        $teams = DB::table('teams')
            ->join('groups', 'teams.group_id', '=', 'groups.id')
            ->select('groups.*', 'teams.*')
            ->where('groups.verified', 0)
            ->where('groups.status', '=', 1)
            ->where('groups.deleted', '=', 0)
            ->paginate(10);
        $teamsInfo = $this->getData($teams);
        // return $teamsInfo;

        return $teamsInfo;
    }
    private function getData($array)
    {
        $groupCatObj = new GroupCategoriesController;
        foreach ($array as $key => &$group) {
            $admin = Group_member::select('user_id')->where('group_id', $group->group_id)->get()->first();
            $userInfo = User::find($admin->user_id);
            $group->admin_name = $userInfo->first_name . " " . $userInfo->last_name;
            $group->admin_id = $userInfo->id;
            $group->admin_email = $userInfo->email;
            $group->categories = $groupCatObj->getTeamCategories($group->id);
            if ($group->image != "") {
                $group->image = asset('images/companies/' . $group->image);
            } else {
                $group->image = asset('images/profile-pic.jpg');
            }
            $country = Countries::find($group->country);
            if ($country != "") {
                $group->country = $country->name;
            } else {
                $group->country = "Unset";
            }

        }
        return $array;
    }
    function getTeamById($id){
        $team = DB::table('teams')
            ->join('groups', 'teams.group_id', '=', 'groups.id')
            ->select('groups.*', 'teams.*')
            ->where('groups.id', $id)
            ->get();
        $teamInfo = $this->getData($team)->first();
        return $teamInfo;
    }
    function getAllTeams(){
        $team = DB::table('teams')
            ->join('groups', 'teams.group_id', '=', 'groups.id')
            ->select('groups.*', 'teams.*')
            ->where('groups.status', 1)
            ->get();
        $teamInfo = $this->getData($team);
        return $teamInfo;
    }
}
