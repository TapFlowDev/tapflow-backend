<div class="mb-3">
    <label for="first_name" class="form-label">Category Name</label>
    <input type="text" name="name" required class="form-control" id="name" aria-describedby="emailHelp" value="{{ old('name') }}@isset($category){{ $category->name }}@endisset">
    @error('name')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<button type="submit" class="btn btn-primary">Submit</button>
