<div class="mb-3">
    <label for="label" class="form-label">Email templete*</label>
    <input type="text" name="email_template" required @isset($notification) disabled @endisset class="form-control" id="email_template" aria-describedby="emailHelp"
        value="{{ old('email_template') }}@isset($notification){{ $notification->email_template }}@endisset">
    @error('email_template')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="type" class="form-label">Type</label>
    <select name="type" class="form-control" id="type">
        <option value="1" @isset($notification) @if($notification->field_type==1) selected @endif @endisset>Agency member</option>
        <option value="2" @isset($notification) @if($notification->field_type==2) selected @endif @endisset>Client</option>
        <option value="3" @isset($notification) @if($notification->field_type==3) selected @endif @endisset>All</option>
    </select>
    @error('type')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="email_subject" class="form-label">Email Subject</label>
    <input type="text" name="email_subject" class="form-control" id="email_subject" aria-describedby="emailHelp"
        value="{{ old('email_subject') }}@isset($notification) {{ $notification->email_subject }} @endisset"
        required>
    @error('email_subject')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="email_text" class="form-label">Email Text</label>
    <textarea name="email_text" class="form-control" id="email_text" rows="4" aria-describedby="emailHelp">{{ old('email_text') }}@isset($notification)
{{ $notification->email_text }}
@endisset</textarea>
@error('email_text')
    <span class="invalid-feedback" style="display: block;" role="alert">
        {{ $message }}
    </span>
@enderror
</div>

{{-- <div class="mb-3">
    <label for="notification_title" class="form-label">Notification Title</label>
    <input type="text" name="notification_title" class="form-control" id="notification_title" aria-describedby="emailHelp"
        value="{{ old('notification_title') }}@isset($notification) {{ $notification->notification_title }} @endisset"
        required>
    @error('email_subject')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="notification_text" class="form-label">Notification Text</label>
    <textarea name="notification_text" class="form-control" id="notification_text" rows="4" aria-describedby="emailHelp">{{ old('bio') }}@isset($notification)
{{ $notification->notification_text }}
@endisset</textarea>
@error('email_text')
    <span class="invalid-feedback" style="display: block;" role="alert">
        {{ $message }}
    </span>
@enderror
</div> --}}


<div class="mb-3 form-check">
    <input class="form-check-input" type="checkbox" id="has_group_name" name="has_group_name" value="1" @isset($notification) @if($notification->required==1) checked @endif @endisset>
    <label for="has_group_name" class="form-check-label">does agency or comapny name appers in the text?</label>
    @error('has_group_name')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<button type="submit" class="btn btn-primary">Submit</button>
