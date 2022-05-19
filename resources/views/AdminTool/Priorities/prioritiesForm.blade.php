<div class="mb-3">
    <label for="name" class="form-label">Name*</label>
    <input type="text" name="name" required class="form-control" id="name" aria-describedby="emailHelp" value="{{ old('name') }}@isset($priority){{ $priority->name }}@endisset">
    @error('name')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="score" class="form-label">Score</label>
    <input type="number" name="score" class="form-control" id="score" aria-describedby="emailHelp" value="{{ old('score') }}@isset($priority){{ $priority->score }}@endisset">
    @error('score')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
{{-- <div class="mb-3">
    <label for="email" class="form-label">Email address</label>
    <input type="email" name="email" class="form-control" id="user_email" aria-describedby="emailHelp" value="{{ old('email') }} @isset($user){{ $user->email }}@endisset">
    @error('email')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div> --}}
<button type="submit" class="btn btn-primary">Submit</button>
