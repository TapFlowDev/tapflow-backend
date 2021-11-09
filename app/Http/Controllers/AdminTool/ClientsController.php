<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use App\Models\Group;
use App\Models\Company;
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
            ->join('companies', 'clients.company_id', '=', 'companies.id')
            ->join('groups', 'companies.group_id', '=', 'groups.id')
            ->select('clients.*', 'users.*', 'clients.id as client_id', 'groups.name as company_name')->paginate(10);
        // dd($freeLancers);
        return view('AdminTool.clients.index', ['users' => $clients]);
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
        $info = $this->getUserData(Client::where('id', 1)->get());

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
            $teamInfo = Company::find($user->company_id);
            $groupName = Group::find($teamInfo->group_id)->name;

            $user->first_name = $userInfo->first_name;
            $user->last_name = $userInfo->last_name;
            $user->company_name = $groupName;
        }
        return $array;
    }
}