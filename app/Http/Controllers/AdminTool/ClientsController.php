<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use App\Models\Group;
use App\Models\Company;
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
            ->select('users.*', 'clients.*')->paginate(10);
        // dd($freeLancers);
        $users = $this->getUserData($clients);

        return view('AdminTool.clients.index', ['users' => $users]);
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
        $info = $this->getUserData(Client::where('user_id', $id)->get());

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
                $companyId = $groupId->group_id;
                $groupName = Group::find($groupId->group_id)->name;
            }else{
                $companyId = "";
                $groupName ='no company yet';
            }
            // $teamInfo = Team::find($groupId);
            
            $user->first_name = $userInfo->first_name;
            $user->last_name = $userInfo->last_name;
            $user->company_id = $companyId;
            $user->company_name = $groupName;
        }
        return $array;
    }
}