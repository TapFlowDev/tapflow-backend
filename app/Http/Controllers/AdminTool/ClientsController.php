<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use App\Models\Group;
use App\Models\Company;
use App\Models\Countries;
use App\Models\Group_member;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Echo_;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = DB::table('clients')
            ->join('users', 'clients.user_id', '=', 'users.id')
            ->select('users.*', 'clients.*')
            ->orderBy('users.created_at', 'desc')
            ->paginate(20);
        // dd($freeLancers);
        $users = $this->getUserData($clients);

        return view('AdminTool.Clients.index', ['users' => $users]);
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
      $member = DB::table('clients')
        ->join('users', 'clients.user_id', '=', 'users.id')
        ->select('users.*', 'clients.*')
        ->where('users.id', $id)
        ->get();
        // return $member;
        $memberInfo = $this->getUserData($member)->first();
        return view('AdminTool.Clients.show',  ['info' => $memberInfo]);

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
            if (isset($groupId) && $groupId != '') {
                $teamId = $groupId->group_id;
                $groupInfo = Group::find($teamId);
                $groupName = $groupInfo->name;
                $groupVerfied = $groupInfo->verified;
                // dd($groupName);
            } else {
                $teamId = "";
                $groupName = 'no team yet';
                $groupVerfied = '0';
            }
            if($user->image != ""){
                $user->image = asset('images/users/' . $user->image);
            }else{
                $user->image = asset('images/profile-pic.jpg');
            }
            // $teamInfo = Team::find($groupId);
            $country = Countries::find($user->country);
            if ($country != "") {
                $user->country = $country->name;
            } else {
                $user->country = "Unset";
            }

            $user->first_name = $userInfo->first_name;
            $user->last_name = $userInfo->last_name;
            $user->full_name = $user->first_name . " " . $user->last_name;
            $user->team_id = $teamId;
            $user->team_name = $groupName;
            $user->group_verfied = $groupVerfied;
        }
        return $array;
    }
}