@component('mail::message')
Propsal id :
{{ $details['propsal_id'] }}

@component('mail::button', {{ $details['url'] }})
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
