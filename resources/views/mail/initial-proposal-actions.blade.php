@component('mail::message')

    <body style="font-family: Helvetica,Arial,sans-serif;">
        <p style="color: black;">
            @if ($details['status'] == 1)
                @if ($details['projectType'] == 3)
                    We are glad to inform you that
                    Your Application to Project {{ $details['projectName'] }} has been accepted.
                    The next step is to send a Contract to the client from the project page.
                    You can agree with the client on the final terms by contacting them on this
                    email:{{ $details['clientEmail'] }}
                @else
                    We are glad to inform you that
                    Your Initial Proposal to Project {{ $details['projectName'] }} has been accepted.
                    The next step is to send a final proposal to the client from the project page.
                    You can agree with the client on the final terms by contacting them on this
                    email:{{ $details['clientEmail'] }}
                @endif
            @elseif ($details['status'] == 2)
                @if ($details['projectType'] == 3)
                    We are sorry to inform you that
                    Your Application to Project {{ $details['projectName'] }} has been rejected.
                @else
                    We are sorry to inform you that
                    Your Initial Proposal to Project {{ $details['projectName'] }} has been rejected.
                @endif
            @endif
            <br>
            <br>
        </p>
    </body>
@endcomponent
