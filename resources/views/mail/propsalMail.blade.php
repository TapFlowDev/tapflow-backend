@component('mail::message')

    <body style="font-family: Helvetica,Arial,sans-serif;">
        <h1 style="color: black; font-weight: bold;">Hey {{ $details['admin_name'] }},</h1>
        <p style="color: black;">
            You have received a new initial proposal from {{ $details['team_info']['name'] }}
            <br>
            <br>
        </p>
        <p style="color: black; font-weight: bold;">
            Here is the proposal:
        </p>
        <table role="presentation" style="border:'0'; color:black;  font-size: medium" cellspacing="0" width="100%">
            <tr>
                <td>Project Name:</td>
                <td>{{ $details['project_name'] }}</td>
            </tr>
            <tr>
                <td>Estimated price:</td>
                <td>${{ $details['proposal']['price_min'] }} - ${{ $details['proposal']['price_max'] }} </td>
            </tr>
            <tr>
                <td>Why us:</td>
                <td>{{ $details['proposal']['our_offer'] }}</td>
            </tr>
        </table>
        <br>
        <p style="color: black; font-weight: bold;">
            Some info about the agency:
        </p>
        <table role="presentation" style="border:'0'; color:black;  font-size: medium" cellspacing="0" width="100%">
            <tr>
                <td>Country:</td>
                <td>{{ $details['team_info']['country'] }}</td>
            </tr>
            <tr>
                <td>Agency size:</td>
                <td>{{ $details['team_info']['employees_number'] }}</td>
            </tr>
            <tr>
                <td>Website:</td>
                <td>{{ $details['team_info']['link'] }}</td>
            </tr>
        </table>
        <br>
        <p style="color: black; font-weight: bold;">
            Please reply to this email if you would like to connect
        </p>
        <br>
        <p style="color: black;">
            Good luck,
        </p>
    </body>
@endcomponent