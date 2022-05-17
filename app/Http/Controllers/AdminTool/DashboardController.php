<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AdminTool\TeamsController;
use App\Http\Controllers\AdminTool\AdminConroller;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Company;
use App\Models\Group;
use App\Models\Waiting_List;
use App\Models\Project;
use App\Models\proposal;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    function index()
    {
        $teamsObj = new TeamsController;
        // // $usersObj = new AdminConroller;
        $users = $this->getUseresNotComplete();
        $teams = $teamsObj->getUnverifiedAgencies();
        $stats = $this->getStats();
        // return $stats;

        // return $users;
        return view('AdminTool.dashboard', ['users' => $users, 'teams' => $teams, 'stats' => $stats]);
    }
    private function getStats()
    {
        $projectsObj = new ProjectsController;
        $agency = Group::where('type', '=', 1)->where('status', '=', 1)->count();
        $company = Group::where('type', '=', 2)->where('status', '=', 1)->count();
        // $waitingList = Waiting_List::count();
        $projectRequests = Project::where('status', '<>', 2)->where('status', '<>', -1)->count();
        $project = Project::where('status', 2)->count();
        $data = array(
            'agency' => $agency,
            'company' => $company,
            // 'waitingList' => $waitingList,
            'project' => $project,
            'projectRequests' => $projectRequests
        );
        return $data;
    }
    private function getUseresNotComplete()
    {
        $group_members = DB::table('group_members')->select('user_id')->pluck('user_id')->toArray();
        $users = DB::table('users')->whereNotIn('id', $group_members)
            ->where('type', '<>', 0)
            ->where('status', '=', 1)
            ->where('deleted', '=', 0)
            ->latest()
            ->paginate(10);

        return $users;
    }
}
