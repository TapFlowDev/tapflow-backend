@component('mail::message')
# Welcome to Tapflow

Say hi

@component('mail::button', ['url' => $details['link']])
Join Team
@endcomponent
<br>
Or join by copying this code: 
<br>
{{  $details['code'] }} 

Thanks,<br>
{{ config('app.name') }}
@endcomponent
