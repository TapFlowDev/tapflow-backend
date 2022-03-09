<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Client;
use App\Models\Company;
use App\Models\Group;
use App\Models\Group_member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DummyCompaines extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = DB::table('companies')
            ->join('groups', 'companies.group_id', '=', 'groups.id')
            ->select('groups.*', 'companies.*')
            ->where('type', '=', -1)
            ->orderBy('groups.created_at', 'desc')
            ->paginate(20);

        return view('AdminTool.DummyCompanies.index', ['users' => $companies]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $targets = Category::where('type', '=', 3)->get();
        $industry = Category::where('type', '=', 4)->get();
        return view('AdminTool.DummyCompanies.add', ['industry'=>$industry, 'targets'=>$targets]);
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
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|max:16',
            'company_name' => 'required|max:255',
        ]);
        $teamObj = new CompanyController;

        $user = User::create($req->only('first_name', 'last_name', 'email', 'password', 'role') + ['name' => $req->first_name . " " . $req->last_name, 'type' => -1]);
        $array = array("user_id" => $user->id, 'type_freelancer' => 2);
        $client = Client::create($array);
        if ($req->hasFile('image')) {
            $destPath = 'images/users';
            // $ext = $req->file('image')->getClientOriginalExtension();
            // $imageName = "user-image-" . $userId . "." . $ext;
            // $imageName = now() . "-" . $req->file('image')->getClientOriginalName();
            $imageName = time() . "-" . $req->file('image')->getClientOriginalName();
            // $imageName = $req->file('image') . "user-image-" . $userId . "." . $ext;

            $img = $req->image;

            $img->move(public_path($destPath), $imageName);
            $this->updateFiles($user->id, $imageName, 'image');
        }
        $group = Group::create(['name' => $req->company_name,'type' => -1, 'admin_id' => $user->id]);
        $teamArr = array();
        $teamArr['group_id'] = $group->id;
        $teamArr['bio'] = $req->bio;
        // $teamArr['link'] = $req->link;
        // $teamArr['country'] = $req->country;
        // $teamArr['employees_number'] = $req->employees_number;
        $teamArr['field'] = $req->field;
        $teamArr['sector'] = $req->sector;
        Company::create($teamArr);
        $groupMemberData = [
            "group_id" => $group->id,
            "user_id" => $user->id,
            "privileges" => 1
        ];
        Group_member::create($groupMemberData);
        if ($req->hasFile('company_image')) {
            $destPath = 'images/companies';
            // $ext = $req->file('image')->extension();
            $imageName = time() . "-" . $req->file('company_image')->getClientOriginalName();
            // $imageName = $req->file('image') . "user-image-" . $userId . "." . $ext;
            $img = $req->company_image;
            $img->move(public_path($destPath), $imageName);
            $teamObj->updateFiles($group->id, $imageName, 'image');
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
        //
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
    function updateFiles($userId, $imageName, $filedName)
    {
        Client::where('user_id', $userId)->update(array($filedName => $imageName));
    }
}