@component('mail::message')
    <body style="font-family: Helvetica,Arial,sans-serif;">
        <h1 style="color: black; font-weight: bold;">Hey {{ $details['name'] }}</h1>
        <p style="color: black;">
            The milestone you have submitted was accepted from the company.
            <br>
            <br>
        </p>
        <p style="color: black; font-weight: bold;">
           Milestone Details:
        </p>
        <table role="presentation" style="border:0; color:black;  font-size: medium" cellspacing='0'width="100%" >
            <tr>
                <td >Project Name:</td>
                <td >{{ $details['project_name'] }}</td>
            </tr> 
            <tr>
                <td >Milestone Name:</td>
                <td >{{ $details['milestone']['name'] }}</td>
            </tr>
            <tr>
                <td>Client FeedBack:</td>
            </tr>
             <tr>
             <td colspan="2">{{ $details['milestone']['client_comments'] }}</td>
            </tr>
        </table>
        <br>
        <a href="https://www.tapflow.app/a-user/main/active-projects/{{ $details['project_id'] }}" target="_blank"
            style="padding: 8px 12px; border: 1px solid #ffc900;border-radius: 10px;font-family: Helvetica, Arial, sans-serif;font-size: medium; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block; background-color: #E57128">
           Open Project
        </a>
        <br>
        <br>
        <p style="color: black;">
        Congratulations
        Keep Going,
        </p>
    </body>
@endcomponent