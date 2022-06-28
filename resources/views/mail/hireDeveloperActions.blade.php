@component('mail::message')

    <body style="font-family: Helvetica,Arial,sans-serif;">
        <h1 style="color: black; font-weight: bold;">Hey {{ $details['name'] }}</h1>
        <p style="color: black;">
            @if ($details['type'] == 1)
                The contract you have submitted was accepted from the company.
            @elseif ($details['type'] == 2)
                We are sorry to inform you that your contract for <strong> {{ $details['project_name'] }} </strong> project has been rejected.
            <!-- @elseif ($details['type'] == 3) -->
                <!-- The final proposal you have submitted was revised from the company. -->
            @endif
        </p>
        <br>
        <p style="color: black; font-weight: bold;">
            Contract Details:
        </p>
        <table role="presentation" style="border:0; color:black; font-size: medium" cellspacing='0' width="100%">
            <tbody>
                <tr>
                    <td>Project Name:</td>
                    <td>{{ $details['project_name'] }}</td>
                </tr>
                <tr>
                    <td>Project Type:</td>
                    <td>Hire Developers</td>
                </tr>
            </tbody>
        </table>
        <br>
        <a href="https://www.tapflow.app/a-user/main/pending-project/{{ $details['project_id'] }}" target="_blank"
            style="padding: 8px 12px; border: 1px solid #ffc900;border-radius: 10px;font-family: Helvetica, Arial, sans-serif;font-size: medium; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block; background-color: #E57128">
            Open Project
        </a>
        <br>
        <p style="color: black;">
            @if ($details['type'] == 1)
                Congratulations
                Good luck,
            @elseif ($details['type'] == 2)
                Good luck Next Time,
            <!-- @elseif ($details['type'] == 3) -->
            @endif
        </p>
    </body>
@endcomponent