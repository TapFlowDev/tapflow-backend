@component('mail::message')
Reset your Password
{{-- <a href="{{ $details['url'] }}">Click here to reset your password</a> --}}

@component('mail::button', ['url' => $details['url']])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
