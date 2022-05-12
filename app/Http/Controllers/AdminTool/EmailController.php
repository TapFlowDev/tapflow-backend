<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\CustomMail;
use App\Mail\ProjectMail;
use App\Mail\ProposalMail;
use App\Models\Group;
use App\Models\Project;
use App\Models\proposal;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;
use Exception;

class EmailController extends Controller
{
    function show($id)
    {
        $user = User::find($id);
        return view('AdminTool.Email.sendEmail', ['user' => $user]);
    }
    function send(Request $req)
    {

        try {
            $details = [
                "subject" => $req->subject,
                "content" => $req->content
            ];
            Mail::to($req->email)->send(new CustomMail($details));
            $req->session()->flash('success', 'email sent successfully');
        } catch (\Exception $error) {
            $req->session()->flash('error', 'email was not sent due to an error');
        }
        // Mail::to($req->email)->send(new CustomMail($details));
        return redirect()->back();
    }
    function sendEmailToAgencies($agencies, $project){
        foreach($agencies as $agency){
            $details = [
                "subject" => 'We have found you a great project',
                "name" => $agency->admin_name,
                "project" => $project
            ];
            Mail::mailer('smtp2')->to($agency->admin_email)->send(new ProjectMail($details));
        }
        return 1;
        // return 1;

    }
    function sendStaticMail($proposalId){
        // $proposal_id = $req->id;
        $propsal = proposal::find($proposalId);
        // dd($propsal);
        $projectData = Project::find($propsal->project_id);
        $teamData = Group::find($projectData->team_id);
        $companyAdminData = User::find($projectData->user_id);
        $teamInfo['name'] = $teamData->name;

        $details = [
            'subject' => 'Initial Proposal ' . $projectData->name,
            'project_id' => $projectData->id,
            'team_info' => $teamInfo,
            'admin_name' => $companyAdminData->first_name
        ];
        Mail::mailer('smtp2')->to('hamzahshajrawi@gmail.com')->send(new ProposalMail($details));
        // Mail::mailer('smtp2')->to($companyAdminData->email)->send(new ProposalMail($details));
        // Mail::mailer('smtp2')->to('abed@tapflow.app')->send(new ProposalMail($details));
        // Mail::mailer('smtp2')->to('naser@tapflow.app')->send(new ProposalMail($details));
        return $companyAdminData->email;
    }
}
