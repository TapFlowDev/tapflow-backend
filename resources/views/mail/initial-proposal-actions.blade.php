@component('mail::message')

    <body style="font-family: Helvetica,Arial,sans-serif;">
        <p style="color: black;">
            @if ($details['status'] == 1)
            We are glad to inform you that 
            Your Initial Proposal to Project {{ $details['projectName'] }} has been accepted.
            @elseif ($details['status'] == 2)
            We are sorry to inform you that 
            Your Initial Proposal to Project {{ $details['projectName'] }} has been rejected.
            @endif
            <br>
            <br>
        </p>
    </body>
@endcomponent
