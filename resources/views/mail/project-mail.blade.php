{{-- @component('mail::message') --}}
<body style="font-family: Helvetica,Arial,sans-serif;">
    <h1 style="color: black; font-weight: bold;">Hey {{ $details['name'] }},</h1>
    <p style="color: black;">
        We have a project that we think is a great fit. Here are the info:
        <br>
        <br>
    </p>
    <table role="presentation" style="border:'0'; color:black;  font-size: medium" cellspacing="0" width="100%">
        <tr style="margin-bottom: 2px">
            <td>Title:</td>
            <td>{{ $details['project']['name'] }}</td>
        </tr>
        <tr style="margin-bottom: 2px">
            <td>Duration:</td>
            <td>{{ $details['project']['duration'] }}</td>
        </tr>
        <tr style="margin-bottom: 2px">
            @if ($details['project']['type'] != 3)
                <td>Budget:</td>
            @else
                <td>Hourly Budget:</td>
            @endif
            <td>{{ $details['project']['budget'] }} </td>
        </tr>
        <tr style="margin-bottom: 2px">
            <td>Requirements:</td>
            <td>
                @foreach ($details['project']['requirments_description'] as $req)
                    {{ $req }}
                    @if (!$loop->last)
                        <br>
                    @endif
                @endforeach
            </td>
        </tr>
    </table>
    <br>
    <p style="color: black;">
        If you're interested, please apply from <a
            href="https://www.tapflow.app/a-user/main/project-info/{{ $details['project']['id'] }}" target="_blank"
            style=" color: black;text-decoration: none;font-weight:bold;">
            Here
        </a>.
    </p>
    <br>
    <p style="color: black;">
        Thanks,
    </p>
    <br>
    <p style="color: black;">
        Tapflow Projects,
    </p>
</body>
{{-- @endcomponent --}}
