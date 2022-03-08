@component('mail::message')
    <body style="font-family: Helvetica,Arial,sans-serif;">
        <h1 style="color: black; font-weight: bold;">Hey {{ $details['name'] }}</h1>
        <p style="color: black;">
            We think that your agency is the right fit for a project. Please check the project details below:
            <br>
            <br>
        </p>
        <p style="color: black; font-weight: bold;">
            Project Details:
        </p>
        <table role="presentation" style="border:'0'; color:black;  font-size: medium" cellspacing="0" width="100%">
            <tr>
                <td>Project Name:</td>
                <td>{{ $details['project']['name'] }}</td>
            </tr>
            <tr>
                <td>Description:</td>
                <td>{{ $details['project']['description'] }}</td>
            </tr>
            <tr>
                <td>Duration:</td>
                <td>{{ $details['project']['duration'] }}</td>
            </tr>
            <tr>
                @if ($details['project']['budget_type'] > 0)
                    <td>Budget:</td>
                    <td>Not Specified</td>
                @else
                    <td>Budget:</td>
                    <td>${{ $details['project']['min'] }} - ${{ $details['project']['max'] }} </td>
                @endif
            </tr>
        </table>
        <br>
        <a href="https://www.tapflow.app/a-user/main/project/{{ $details['project']['id'] }}" target="_blank"
            style="padding: 8px 12px; border: 1px solid #ffc900;border-radius: 10px;font-family: Helvetica, Arial, sans-serif;font-size: medium; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block; background-color: #ffc900">
            View & Apply
        </a>
        <br>
        <br>
        <p style="color: black;">
            Good luck,
        </p>
    </body>
@endcomponent