@component('mail::message')

    <body style="font-family: Helvetica,Arial,sans-serif;">
        <table role="presentation" style="border:'0'; color:black;" cellspacing="0" width="100%">
            <tr>
                <td>
                    <h2 style="color: black;">Hey {{ $details['admin_name'] }},</h2>
                </td>
            </tr>
            <tr>
                <td>
                    @if ($details['mail_type'] == 1)
                        <p style="color: black;">Your Candidates for Project {{ $details['project_name'] }} has been
                            reviewed,
                            <a href="https://www.tapflow.app/a-user/main/project/{{ $details['project_id'] }}"
                                target="_blank" style=" color: black;text-decoration: none;font-weight:bold;">
                                Check it out
                            </a>
                        </p>
                    @else
                    <p style="color: black;">You have received new candidates for project {{ $details['project_name'] }},
                        <a href="https://www.tapflow.app/Client-user/main/project-info/{{ $details['project_id'] }}"
                            target="_blank" style=" color: black;text-decoration: none;font-weight:bold;">
                            Check it out
                        </a>
                    </p>
                    @endif
                </td>
            </tr>
        </table>
    </body>
@endcomponent
