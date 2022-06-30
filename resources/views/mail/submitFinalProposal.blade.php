@component('mail::message')
<body style="font-family: Helvetica,Arial,sans-serif;">
    <h1 style="color: black; font-weight: bold;">Hey {{ $details['name'] }}</h1>
    <p style="color: black;">
        The {{$details['agency_name']}} have finished and submitted the final proposal.
        <br>
        <br>
    </p>
    <p style="color: black; font-weight: bold;">
        Proposal Details:
    </p>
    <table role="presentation" style="border:0; color:black;  font-size: medium" cellspacing='0' width="100%">
    <tbody>
        <tr>
            <td>Project Name:</td>
            <td>{{ $details['project_name'] }}</td>
        </tr>
        <tr>
            <td>Proposal Description:</td>
        </tr>
        <tr>
            <td colspan="2">{{ $details['Proposal_description'] }}</td>
        </tr>
        </tbody>
    </table>
    <br>
    <a href="https://www.tapflow.app/Client-user/main/project-info/{{ $details['project_id'] }}" target="_blank" style="padding: 8px 12px; border: 1px solid #ffc900;border-radius: 10px;font-family: Helvetica, Arial, sans-serif;font-size: medium; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block; background-color: #ffc900">
        Open Project
    </a>
    <br>
    <br>
    <p style="color: black;">
        Good Luck,
    </p>
</body>
@endcomponent