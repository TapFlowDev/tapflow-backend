<div class="mb-3">
    <label for="title" class="form-label">Title</label>
    <input type="text" name="title"  class="form-control" id="title" 
        value="{{ old('title') }}@isset($data->title){{ $data->title }}@endisset">
    @error('title')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="imageContent" class="form-label">Image</label>
    <input class="form-control" name="imageContent" type="file" id="imageContent">
    @isset($data->image)
        <a class="" style="color:black" target="_blanck" href="{{ asset('images/content/' . $data->image) }}" role="">View
            Image</a>
    @endisset
    @error('imageContent')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="formFile" class="form-label">Link</label>
    <input type="link" name="link"  class="form-control" id="link" 
        value="{{ old('link') }}@isset($data->link){{ $data->link }}@endisset">
    @error('link')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="text" class="form-label">Text</label>
    <input type="text" name="text"  class="form-control" id="text" 
        value="{{ old('text') }}@isset($data->text){{ $data->text }}@endisset">
    @error('text')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="big_text" class="form-label">Big Text</label>
    <textarea  name="big_text" class="ckeditor form-control" id="big_text" aria-describedby="emailHelp" >{{ old('big_text') }}@isset($data->big_text){{ $data->big_text }}@endisset</textarea>
    @error('big_text')
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
<script type="text/javascript">
    $(document).ready(function () {
        $('.ckeditor').ckeditor();
    });
</script>
