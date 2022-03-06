<div class="mb-3">
    <label for="label" class="form-label">Question</label>
    <input type="text" name="label" required class="form-control" id="label" aria-describedby="emailHelp"
        value="{{ old('label') }}@isset($option){{ $option->label }}@endisset">
    @error('label')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="field_type" class="form-label">Field Type</label>
    <select name="field_type" class="form-control" id="field_type">
         
        <option value="1" @isset($option) @if($option->field_type==1) selected @endif @endisset>Text Field</option>
        <option value="2" @isset($option) @if($option->field_type==2) selected @endif @endisset>Text Area</option>
    </select>
    @error('field_type')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3 form-check">
    <input class="form-check-input" type="checkbox" id="required" name="required" value="1" @isset($option) @if($option->required==1) checked @endif @endisset>
    <label for="required" class="form-check-label">is required</label>
    @error('required')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<button type="submit" class="btn btn-primary">Submit</button>
