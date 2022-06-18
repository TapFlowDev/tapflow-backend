
<div class="mb-3">
    <h5>Hourly rate:</h5>
    <div class="row">
        <div class="col-6">
            <label for="minPerHour" class="form-label">Minimum:</label>
            <input type="text" name="minPerHour" class="form-control" placeholder="Price in USD" id="minPerHour" aria-describedby="emailHelp"
                value="{{ old('minPerHour') }}@if($team->minPerHour!='') {{ $team->minPerHour }} @endif">
            @error('minPerHour')
                <span class="invalid-feedback" style="display: block;" role="alert">
                    {{ $message }}
                </span>
            @enderror
        </div>
        <div class="col-6">
            <label for="maxPerHour" class="form-label">Maximum:</label>
            <input type="text" name="maxPerHour" class="form-control" id="maxPerHour" placeholder="Price in USD" aria-describedby="emailHelp"
                value="{{ old('maxPerHour') }}@if($team->maxPerHour!='') {{ $team->maxPerHour }} @endif">
            @error('maxPerHour')
                <span class="invalid-feedback" style="display: block;" role="alert">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
</div>
<div class="mb-3">
    <h5>Working hours per day:</h5>
    <div class="row">
        <div class="col-6">
            <label for="min_work_hour" class="form-label">Minimum:</label>
            <input type="text" name="min_work_hour" class="form-control" id="min_work_hour" placeholder="hours" aria-describedby="emailHelp"
                value="{{ old('min_work_hour') }}@if($team->min_work_hour!='') {{ $team->min_work_hour }} @endif">
            @error('min_work_hour')
                <span class="invalid-feedback" style="display: block;" role="alert">
                    {{ $message }}
                </span>
            @enderror
        </div>
        <div class="col-6">
            <label for="maxPerHour" class="form-label">Maximum:</label>
            <input type="text" name="max_work_hour" class="form-control" id="max_work_hour" placeholder="hours" aria-describedby="emailHelp"
                value="{{ old('max_work_hour') }}@if($team->max_work_hour!='') {{ $team->max_work_hour }} @endif">
            @error('max_work_hour')
                <span class="invalid-feedback" style="display: block;" role="alert">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
</div>
<div class="mb-3">
    <label for="lead_time" class="form-label">Lead Time:</label>
    <select name="lead_time" class="form-control" id="type">
        @if($team->lead_time=='')
        <option value=""> Select number of days</option>
        @endif
        @for ($i = 1; $i <= 31; $i++)
        <option value="{{ $i }}" @isset($team) @if($team->lead_time==$i) selected @endif @endisset> {{ $i }} day(s)</option>
        @endfor
    </select>
    @error('lead_time')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="judgment" class="form-label">Judgment</label>
    <input type="number" min="0.0" max="10.0" step="0.1" name="judgment" class="form-control" id="judgment" aria-describedby="emailHelp"
        value="{{ old('judgment') }}@isset($team){{ $team->judgment }}@endisset">
    @error('judgment')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="years_of_experience" class="form-label">Years of Experience</label>
    <input  type="number" min="0" step="1"  name="years_of_experience" class="form-control" id="years_of_experience" aria-describedby="emailHelp"
        value="{{ old('years_of_experience') }}@isset($team){{ $team->years_of_experience }}@endisset">
    @error('years_of_experience')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <label for="response_time" class="form-label">Response Time</label>
    <select name="response_time" class="form-control" id="type">
        <option value="10" @isset($team) @if($team->response_time==10) selected @endif @endisset> Very Responsive </option>
        <option value="8" @isset($team) @if($team->response_time==8) selected @endif @endisset> Responsive </option>
        <option value="6" @isset($team) @if($team->response_time==6) selected @endif @endisset> Normal </option>
        <option value="4" @isset($team) @if($team->response_time==4) selected @endif @endisset> Not Responsive </option>
        <option value="0" @isset($team) @if($team->response_time==0) selected @endif @endisset> Very Not Responsive </option>
    </select>
    @error('response_time')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<button type="submit" class="btn btn-primary">Submit</button>
