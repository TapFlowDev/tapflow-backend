@component('mail::message')
Name: {{ $details['name'] }}<br>
Email: {{ $details['email'] }}<br>
Email: {{ $details['message'] }}<br>


<br>
{{ config('app.name') }}
@endcomponent
