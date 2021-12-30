{{-- @component('mail::message') --}}
<h1 style="color: black; font-weight: bold;">Welcome to Tapflow,</h1>
<p style="color: black;">
You Recived an invitation to join your team in Tapflow!
<br>
<a href="https://www.tapflow.app">Tapflow link</a>
<br>
Please enter this code after logging into <a href="https://www.tapflow.app">Tapflow</a> to join your team members : <strong>{{  $details['code'] }} </strong>
<br>
{{-- @component('mail::button', ['url' => $details['link']])
Join Team
@endcomponent
<br>
Or join by copying this code: 
<br>
{{  $details['code'] }}  --}}
{{-- 
Thanks,<br> --}}
<span style="color: black;">{{ config('app.name') }},</span>
</p>
{{-- @endcomponent --}}
