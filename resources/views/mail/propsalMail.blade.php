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
                    <p style="color: black;">You have received a new initial proposal from
                        {{ $details['team_info']['name'] }}</p>
                </td>
            </tr>
            <tr>
                <td>
                    <p style="color: black; font-weight: bold; font-size: medium;">Here is the proposal:</p>
                </td>
            </tr>
            <tr>
                <td>
                    <p style="color: black; font-weight: bold; font-size: small;">Project Name:</p>
                    <p style="color: black; font-size: small;">{{ $details['project_name'] }}</p>
                </td>
            </tr>
            <tr>
                <td>
                    <p style="color: black; font-weight: bold; font-size: small;">Estimated price:</p>
                    <p style="color: black; font-size: small;">${{ $details['est']['min'] }} -
                        ${{ $details['est']['max'] }} </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p style="color: black; font-weight: bold; font-size: small;">Why us:</p>
                    <p style="color: black; font-size: small;">{{ $details['proposal']['our_offer'] }}</p>
                </td>
            </tr>
        </table>
        <table role="presentation" style="border:'0'; color:black;  font-size: medium" cellspacing="0" width="100%">
            <tr>
                <td>
                    <p style="color: black; font-weight: bold;">Some info about the agency:</p>
                </td>
            </tr>
            <tr>
                <td>
                    <p style="color: black; font-weight: bold; font-size: small;">Country:</p>
                    <p style="color: black;  font-size: small;">{{ $details['team_info']['country'] }}</p>
                </td>
            </tr>
            <tr>
                <td>
                    <p style="color: black; font-weight: bold; font-size: small;">Agency size:</p>
                    <p style="color: black;  font-size: small;">
                        {{ $details['team_info']['employees_number'] }}</p>
                </td>
            </tr>
            <tr>
                <td>
                    <p style="color: black; font-weight: bold; font-size: small;">Website:</p>
                    <p style="color: black;  font-size: small;">
                        {{ $details['team_info']['link'] }}</p>
                </td>
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
