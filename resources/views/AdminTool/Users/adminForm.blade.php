<input type="hidden" name="type" value="0">
<input type="hidden" name="gender" value="m">
<div class="mb-3">
    <label for="first_name" class="form-label">First Name</label>
    <input type="text" name="first_name" class="form-control" id="first_name" aria-describedby="emailHelp" value="{{ old('first_name') }}@isset($user){{ $user->first_name }}@endisset">
    @error('first_name')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>

<div class="mb-3">
    <label for="last_name" class="form-label">Last Name</label>
    <input type="text" name="last_name" class="form-control" id="last_name" aria-describedby="emailHelp" value="{{ old('last_name') }}@isset($user){{ $user->last_name }}@endisset">
    @error('last_name')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="email" class="form-label">Email address</label>
    <input type="email" name="email" class="form-control" id="user_email" aria-describedby="emailHelp" value="{{ old('email') }} @isset($user){{ $user->email }}@endisset">
    @error('email')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <input type="password" name="password" class="form-control" id="user_password">
    @error('password')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="password_confirmation" class="form-label">Password Confirmation</label>
    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation">
    @error('password')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<button type="submit" class="btn btn-primary">Submit</button>
