@component('mail::message')

<body style="font-family: Helvetica,Arial,sans-serif;">
    <h1 style="color: black; font-weight: bold;">Hey {{ $details['name'] }}</h1>
    <p style="color: black;">
        This is a our final draft.
    </p>
    <p style="color: black; font-weight: bold;">
        Proposal Details:
    </p>
    <table role="presentation" style="border:0; color:black; font-size: medium" cellspacing='0' width="100%">
        <tbody>
            <tr>
                <td>Project Name:</td>
                <td>{{ $details['project_name'] }}</td>
            </tr>
            <tr>
                <td>Estimated Price:</td>
                <td>{{ $details['price'] }}</td>
            </tr>
            <tr>
                <td>Estimated Number OF Hours:</td>
                <td>{{ $details['hours'] }}</td>
            </tr>
            <tr>
            <td><h4>Milestones</h4></td>
            </tr>
            <tr>
                <table>
                    @foreach ($details['milestones'] as $milestone)
                    <tr>
                        <td>{{$milestone['milestone_name']}}:</td>
                    </tr>
                    <tr>
                        <table>
                            <tbody>
                                <tr>
                                    <td>Number of Hours:</td>
                                    <td>{{ $milestone['milestone_hours'] }}</td>
                                </tr>
                                <tr>
                                    <td>Hourly Rate:</td>
                                    <td>{{ $milestone['milestone_hourly_rate'] }}</td>
                                </tr>
                                <tr>
                                    <td>Price:</td>
                                    <td>{{ $milestone['milestone_price'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </tr>
                    <hr>
                    @endforeach
                </table>
            </tr>
        </tbody>
    </table>
    <a href="https://www.tapflow.app/Client-user/main/posted-projects-details/{{ $details['project_id'] }}" target="_blank" style="padding: 8px 12px; border: 1px solid #ffc900;border-radius: 10px;font-family: Helvetica, Arial, sans-serif;font-size: medium; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block; background-color: #ffc900">
        Open Project
    </a>
</body>
@endcomponent