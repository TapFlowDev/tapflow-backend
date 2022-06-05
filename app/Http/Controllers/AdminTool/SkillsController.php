<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use App\Models\Skills;
use Illuminate\Http\Request;

class SkillsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('AdminTool.Skills.index', ['skills' => Skills::paginate(20)]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('AdminTool.Skills.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $skills = $request->skill;
        foreach ($skills as $skill) {
            $skillArr = array(
                'name' => $skill,
                'unique_name' => $this->trimedSkill($skill),
            );
            Skills::create($skillArr);
        }
        return redirect('/AdminTool/skills');
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
        $skill = Skills::where('id','=',$id)->first();
        if(!$skill){
            return back();
        }
        if($skill->deleted==1){
            $skill->deleted=0;
        }else{ 
            $skill->deleted=1;
        }
        $skill->save();
        return back();
    }
    private function trimedSkill($skill)
    {
        $trimed = str_replace(' ', '-', strtolower(trim($skill)));
        return $trimed;
    }
    function isSkillExsits($skill)
    {
        $responseData['status'] = 200;
        $responseData['msg'] = 'success';
        $responseData['data'] = [];
        $trimed = $this->trimedSkill($skill);
        $doesExist = Skills::where('unique_name', '=', $trimed)->first();
        if ($doesExist) {
            $responseData['status'] = 101;
            $responseData['msg'] = 'already exists';
            $responseData['data'] = [];
            return $responseData;
        }
        $responseData['data'] = ['trimed' => $trimed];
        return $responseData;
    }
}
