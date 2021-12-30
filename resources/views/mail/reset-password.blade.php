{{-- @component('mail::message') --}}
<h1 style="color: black; font-weight: bold;">Reset your Password</h1>
<p style="color: black;">
<a href={{ $details['url'] }}>Click here to reset your password</a>
<br>
{{-- @component('mail::button', ['url' => $details['url']])
Button Text
@endcomponent --}}

{{-- Thanks,<br> --}}
<span style="color: black;">{{ config('app.name') }},</span>
</p>
{{-- @endcomponent --}}