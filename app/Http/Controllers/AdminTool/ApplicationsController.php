<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Requirement;
use App\Http\Controllers\TeamController;
use App\Models\Group;
use App\Models\hire_developer_proposals;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ApplicationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        // $teamObj = new TeamsController;
        $proposal = $this->getData(hire_developer_proposals::where('id', '=', $id)->get())->first();
        // return $proposal;
        // $allTeams = $teamObj->getAllTeams();
        return view("AdminTool.Applications.show", ['proposal' => $proposal]);
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
    private function getData($proposals)
    {
        $requirementObj = new Requirement;
        $teamControllersObj = new TeamController;

        foreach ($proposals as $keyP => &$proposal) {
            // $proposal->requirments_description = $requirementObj->getRequirementsByProjectId($proposal->project_id)->pluck('description')->toArray();
            $proposalRequirments = $requirementObj->getHireDevInitialProposalRequirements($proposal->id);
            $proposal->requirementDetails = $proposalRequirments;
            $teamInfo = Group::find($proposal->team_id);
            $projectInfo = Project::find($proposal->project_id);
            $companyInfo = Group::find($projectInfo->company_id);
            $userInfo = User::find($proposal->user_id);
            $proposal->teamName = $teamInfo->name;
            $proposal->companyName = $companyInfo->name;
            $proposal->projectName = $projectInfo->name;
            $proposal->teamAdminName = $userInfo->name;
            $proposal->teamAdminEmail = $userInfo->email;
        }
        return $proposals;
    }
}