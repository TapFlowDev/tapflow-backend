<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Freelancer;
use App\Models\User;
use App\Models\Group;
use App\Models\Group_member;
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
            ->select('users.*', 'freelancers.*')->paginate(10);
        $users = $this->getUserData($freeLancers);
        // print_r(json_encode($users));

        return view('AdminTool.FreeLancers.index', ['users' => $users]);
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
        $info = $this->getUserData(Freelancer::where('user_id', $id)->get());

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
            // $userInfo = $user;
            $groupId = Group_member::select('group_id')
            ->where('user_id', $user->id)->get()->first();
            if(isset($groupId) && $groupId!=''){
                $teamId = $groupId->group_id;
                $groupName = Group::find($groupId->group_id)->name;
            }else{
                $teamId = "";
                $groupName ='no team yet';
            }
            // $teamInfo = Team::find($groupId);
            
            $user->first_name = $userInfo->first_name;
            $user->last_name = $userInfo->last_name;
            $user->team_id = $teamId;
            $user->team_name = $groupName;
        }
        return $array;
    }
}
