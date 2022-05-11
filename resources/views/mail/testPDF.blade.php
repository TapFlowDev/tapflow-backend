@component('mail::message')

    <body style="font-family: Helvetica,Arial,sans-serif;">
        <p style="color: black;">
            @if ($status == 1)
            We are glad to inform you that 
            Your Initial Proposal to Project {{ $projectName }} has been accepted.
            The next step is to send a final proposal to the client from the project page.
            You can agree with the client on the final terms by contacting them on this Email: {{ $clientEmail }}
            @elseif ($status == 2)
            We are sorry to inform you that 
            Your Initial Proposal to Project {{ $projectName }} has been rejected.
            @endif
            <br>
            <br>
        </p>
    </body>
@endcomponent
