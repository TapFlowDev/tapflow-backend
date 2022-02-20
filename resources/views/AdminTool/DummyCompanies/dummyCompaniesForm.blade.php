<div class="mb-3">
    <h3>Company admin info</h3>
</div>
<div class="mb-3">
    <label for="first_name" class="form-label">First Name *</label>
    <input type="text" name="first_name" class="form-control" id="first_name" aria-describedby="emailHelp"
        value="{{ old('first_name') }}@isset($user) {{ $user->first_name }} @endisset"
        required>
    @error('first_name')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>

<div class="mb-3">
    <label for="last_name" class="form-label">Last Name * </label>
    <input type="text" name="last_name" class="form-control" id="last_name" aria-describedby="emailHelp"
        value="{{ old('last_name') }}@isset($user) {{ $user->last_name }} @endisset" required>
    @error('last_name')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="email" class="form-label">Email address *</label>
    <input type="email" name="email" class="form-control" id="user_email" aria-describedby="emailHelp"
        value="{{ old('email') }} @isset($user) {{ $user->email }} @endisset" required>
    @error('email')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="password" class="form-label">Password *</label>
    <input type="password" name="password" class="form-control" id="user_password" required>
    @error('password')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-4">
    <label for="formFile" class="form-label">User Image</label>
    <input class="form-control-file" name="image" type="file" id="user_image">
    @isset($user->image)
        <a class="" style="color:black" target="_blanck"
            href="{{ asset('images/categories/' . $users->image) }}" role="">View
            Image</a>
    @endisset
    @error('image')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<hr>
<div class="mb-3">
    <h3>Company info</h3>
</div>
<div class="mb-3">
    <label for="company_name" class="form-label">Company Name *</label>
    <input type="text" name="company_name" class="form-control" id="company_name" aria-describedby="emailHelp"
        value="{{ old('company_name') }}@isset($user) {{ $user->company_name }} @endisset"
        required>
    @error('company_name')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="bio" class="form-label">Company Bio</label>
        <textarea  name="bio" class="form-control" id="content" rows="4" aria-describedby="emailHelp" >{{ old('bio') }}@isset($user){{ $user->bio }}@endisset</textarea>
    @error('bio')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-4">
    <label for="formFile" class="form-label">Company Image</label>
    <input class="form-control-file" name="company_image" type="file" id="company_image">
    @isset($user->company_image)
        <a class="" style="color:black" target="_blanck"
            href="{{ asset('images/companies/' . $users->company_image) }}" role="">View
            Image</a>
    @endisset
    @error('image')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<button type="submit" class="btn btn-primary">Submit</button>
