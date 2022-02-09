<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Waiting_List;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class AdminConroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $group_members = DB::table('group_members')->select('user_id')->pluck('user_id')->toArray();
        $users = User::whereNotIn('id', $group_members)
            ->where('type', '<>', 0)
            ->where('status', '=', 1)
            ->where('deleted', '=', 0)
            ->latest()
            ->paginate(10);

        return view('AdminTool.Users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('AdminTool.Users.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $validatedData = $request->validated();
        $user = User::create($request->except(['_token']));
        return redirect('/AdminTool/users');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $member = User::select('users.*')
        ->where('users.id', $id)
        ->get();
        $memberInfo = $this->getUserData($member)->first();
        //  return $memberInfo;

        // dd();
        // return $info->first();
        return view('AdminTool.Users.show',  ['info' => $memberInfo]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // dd(User::find($id));
        return view('AdminTool.Users.edit', ['user' => User::find($id)]);
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
        $user = User::findOrfail($id);
        $user->update($request->except(['_token']));
        return redirect('/AdminTool/users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userObj = new User;
        $user = $userObj::find($id);
        $user->status = 0;
        $user->deleted = 1;
        $user->save();
        return redirect('/AdminTool/users');
    }

    public function login($id)
    {
        return view('AdminTool.login');
    }
    private function getUserData($users){
        foreach ($users as $key => &$user) {

            $user->full_name = $user->first_name . " " . $user->last_name;
            if($user->image != ""){
                $user->image = asset('images/users/' . $user->image);
            }else{
                $user->image = asset('images/profile-pic.jpg');
            }
        }
        return $users;
    }
    function waitingList(){
        return view('AdminTool.WaitingList.index', ['users'=> Waiting_List::paginate(10)]);
    }
}
