@component('mail::message')

<body style="font-family: Helvetica,Arial,sans-serif;">
    <p style="color: black;">
        Hey, {{$client_name}} you received final proposal draft from {{$agency_name}}.
    </p>
    <p style="color: black;">
        {{$email_body}}
    </p>
</body>
@endcomponent