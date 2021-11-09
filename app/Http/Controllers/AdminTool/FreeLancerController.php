<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Freelancer;
use App\Models\User;
use App\Models\Group;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Echo_;

class FreeLancerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $freeLancers = DB::table('freelancers')
            ->join('users', 'freelancers.user_id', '=', 'users.id')
            ->join('teams', 'freelancers.team_id', '=', 'teams.id')
            ->join('groups', 'teams.group_id', '=', 'groups.id')
            ->select('freelancers.*', 'users.*', 'freelancers.id as freelancer_id', 'groups.name as team_name')->paginate(10);
        // dd($freeLancers);
        return view('AdminTool.freeLancers.index', ['users' => $freeLancers]);
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
        $info = $this->getUserData(Freelancer::where('id', 1)->get());

        // dd();
        return $info->first();
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

    private function getUserData($array)
    {
        foreach ($array as $key => &$user) {

            $userInfo = User::find($user->user_id);
            $teamInfo = Team::find($user->team_id);
            $groupName = Group::find($teamInfo->group_id)->name;

            $user->first_name = $userInfo->first_name;
            $user->last_name = $userInfo->last_name;
            $user->team_name = $groupName;
        }
        return $array;
    }
}
