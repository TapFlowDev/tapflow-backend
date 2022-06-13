<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use App\Models\Skills;
use Exception;
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
        $skill = Skills::where('id', '=', $id)->first();
        return view('AdminTool.Skills.edit', ['skill' => $skill]);
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
        $skillData = Skills::find($id);
        $skill = $request->name;
        $trimedSkill = $this->trimedSkill($skill);
        $doesExist = Skills::select('unique_name')->where('unique_name', '=', $trimedSkill)->first();
        if (!$doesExist) {
            $skills = array(
                'name' => $skill,
                'unique_name' => $trimedSkill,
            );
            $skillData->update($skills);
            $skillData->save();
            $request->session()->flash('success', 'Skill Edited Successfully');
            return redirect('/AdminTool/skills');
        }
        $request->session()->flash('fail', 'Skill Already Exists');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $skill = Skills::where('id', '=', $id)->first();
        if (!$skill) {
            return back();
        }
        if ($skill->deleted == 1) {
            $skill->deleted = 0;
        } else {
            $skill->deleted = 1;
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
    function addByCSV()
    {
        return view('AdminTool.Skills.addCSV');
    }
    function createByCSV(Request $request)
    {
        // dd($request);
        try {


            $skills = array();
            if ($request->hasFile('CSV')) {
                $file = $request->file('CSV');
                $type = $file->getClientOriginalExtension();
                $real_path = $file->getRealPath();
                // dd($real_path);
                if ($type <> 'csv') {
                    // Alert::error('Wrong file extension', 'Only CSV is allowed')->persistent('close');
                    $request->session()->flash('fail', 'File must be csv');
                    return redirect()->back();
                }
                if (($open = fopen($real_path, "r")) !== FALSE) {
                    while (($data = fgetcsv($open, 1000, ",")) !== FALSE) {
                        $skill = $data[0];
                        $trimedSkill = $this->trimedSkill($skill);
                        $doesExist = Skills::select('unique_name')->where('unique_name', '=', $trimedSkill)->first();
                        if (!$doesExist) {
                            $skills = array(
                                'name' => $skill,
                                'unique_name' => $trimedSkill,
                            );
                            Skills::create($skills);
                        }
                    }
                    fclose($open);
                }
                $request->session()->flash('success', 'Skills Added Successfully');
                return redirect('/AdminTool/skills');
            }
            $request->session()->flash('fail', 'File must be csv');
            return redirect()->back();
        } catch (Exception $error) {
            $request->session()->flash('fail', $error->getMessage());
            return redirect()->back();
        }
    }
}
