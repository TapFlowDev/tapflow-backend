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
                    @if ($details['project_type'] == 3)
                    <p style="color: black;">You have received a new Application from
                        {{ $details['team_info']['name'] }}</p>
                    @else
                        <p style="color: black;">You have received a new initial proposal from
                            {{ $details['team_info']['name'] }}</p>
                    @endif
                </td>
            </tr>
            <tr>
                <td>
                    <a href="https://www.tapflow.app/Client-user/main/project-info/{{ $details['project_id'] }}"
                        target="_blank"
                        style="padding: 8px 12px; border: 1px solid #ffc900;border-radius: 10px;font-family: Helvetica, Arial, sans-serif;font-size: medium; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block; background-color: #ffc900">
                        Check it out
                    </a>
                </td>
            </tr>
        </table>
    </body>
@endcomponent
