<div class="mb-3">
    <label for="content" class="form-label">Content</label>
    <textarea  name="content" class="ckeditor form-control" class="form-control" id="content" aria-describedby="emailHelp" >{{ old('content') }}@isset($announcement){{ $announcement->content }}@endisset</textarea>
        @error('content')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="link" class="form-label">Link</label>
    <input type="text"  name="link" class="form-control" id="link" aria-describedby="emailHelp" value="{{ old('link') }}@isset($announcement){{ $announcement->link }}@endisset">
        @error('link')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>

<div class="mb-3">
    <label for="template" class="form-label">Template</label>
    <select name="template" class="form-control" id="template">
        <option value="1">Template 1</option>
        <option value="2">Template 2</option>
        <option value="3">Template 3</option>
        <option value="4">Template 4</option>
    </select>
    @error('template')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>

<div class="mb-3">
    <label for="formFile" class="form-label">Insert Logo</label>
    <input class="form-control" name="logo" type="file" id="formFile">
    @isset($announcement->logo)
    <a class="text-dark" target="_blanck" href="{{ asset('images/announcements/'.$announcement->logo) }}"
        role="">View logo</a>
    @endisset
    @error('logo')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>

<button type="submit" class="btn btn-primary">Submit</button>
<script type="text/javascript">
    $(document).ready(function () {
        $('.ckeditor').ckeditor();
    });
</script>