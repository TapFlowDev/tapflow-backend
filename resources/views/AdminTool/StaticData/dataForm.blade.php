
<div class="mb-3">
    <label for="first_name" class="form-label">Image</label>
    <input class="form-control" name="imageContent" type="file" id="formImage">
    @isset($data->image)
        <a class="" style="color:black" target="_blanck" href="{{ asset('images/content/' . $data->image) }}" role="">View
            Image</a>
    @endisset
    @error('name')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="formFile" class="form-label">Link</label>
    <input type="text" name="link"  class="form-control" id="link" 
        value="{{ old('link') }}@isset($data->link){{ $data->link }}@endisset">
    @isset($data->image)
        <a class="" style="color:black" target="_blanck" href="{{ asset('images/content/' . $data->image) }}" role="">View
            Image</a>
    @endisset
    @error('link')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="formFile" class="form-label">Text</label>
    <input type="text" name="text"  class="form-control" id="text" 
        value="{{ old('text') }}@isset($data->text){{ $data->text }}@endisset">
   
    @error('image_2')
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
