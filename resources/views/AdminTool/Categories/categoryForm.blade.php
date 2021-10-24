<input type="hidden" name="type" value="0">
<div class="mb-3">
    <label for="first_name" class="form-label">Category Name</label>
    <input type="text" name="name" required class="form-control" id="first_name" aria-describedby="emailHelp" value="{{ old('name') }}@isset($category){{ $category->name }}@endisset">
    @error('name')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="formFile" class="form-label">Insert Image</label>
    <input class="form-control" name="image" type="file" id="formFile">
    @isset($category->image)
    <a class="" target="_blanck" href="{{ asset('images/categories/'.$category->image) }}"
        role="">View Image</a>
    @endisset
    @error('image')
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
