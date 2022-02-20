<input type="hidden" name="company_id" value="{{ $company_id }}">
<div class="mb-3">
    <label for="name" class="form-label">Project Name:</label>
    <input type="text" name="name" class="form-control" id="name" aria-describedby="emailHelp"
        value="{{ old('name') }}@isset($project) {{ $project->name }} @endisset">
    @error('name')
        <span class="invalid-feedback" style="display: block;" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
<div class="mb-3">
    <h5>Project Categories:</h5>
    @foreach ($categories as $category)
        <label for="name" class="form-label">Category Name : {{ $category->name }}</label>
        <select data-placeholder="Choose Agencis" multiple class="chosen-select"
            name='categories[{{ $category->id }}][]'>
            @foreach ($category['subs'] as $sub)
                <option value="{{ $sub->id }}"> {{ $sub->name }}</option>
            @endforeach
        </select>
        <br>
        <br>
    @endforeach
</div>
<div class="mb-3">
    <h5>Project Budget:</h5>
    <div class="row">
        <div class="col-6">
            <label for="min" class="form-label">Minimum:</label>
            <input type="text" name="min" class="form-control" id="min" placeholder="Price in USD" aria-describedby="emailHelp"
                value="{{ old('min') }}@isset($project) {{ $project->min }} @endisset">
            @error('min')
                <span class="invalid-feedback" style="display: block;" role="alert">
                    {{ $message }}
                </span>
            @enderror
        </div>
        <div class="col-6">
            <label for="max" class="form-label">Maximum:</label>
            <input type="text" name="max" class="form-control" id="max" placeholder="Price in USD" aria-describedby="emailHelp"
                value="{{ old('max') }}@isset($project) {{ $project->max }} @endisset">
            @error('max')
                <span class="invalid-feedback" style="display: block;" role="alert">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Project Duration:</label>
    <select class="form-control" id="days" name="days">
        <option value="0"> Select Project Duration</option>
        @foreach ($duration as $day)
        <option value="{{ $day->id }}"> {{ $day->name }}</option>
        @endforeach
      </select>
</div>
<div class="mb-3">
    <label for="description" class="form-label">Project Description:</label>
    <textarea name="description" class="form-control" id="description" rows="5" aria-describedby="emailHelp">{{ old('description') }}@isset($project)
{{ $project->description }}
@endisset</textarea>
@error('description')
    <span class="invalid-feedback" style="display: block;" role="alert">
        {{ $message }}
    </span>
@enderror
</div>
<div class="mb-3" id="projReq">
    <label for="requirements_description" class="form-label">Project Requirements:</label>
    <div class="row" id="dynamicInput[0]">
        <div class="col-8">
            Entry 1<br><input type="text" class="form-control" name="requirements_description[]"> 
        </div>
        <div class="col-4">
            <button type="button" class="btn btn-outline-success mt-4" onClick="addInput();">Add</button>

        </div>
    </div>
@error('requirements_description')
    <span class="invalid-feedback" style="display: block;" role="alert">
        {{ $message }}
    </span>
@enderror
</div>

<button type="submit" class="btn btn-primary">Submit</button>
<script type="text/javascript">
    $(function() {
        $(".chosen-select").chosen();
    });
</script>
<script>
    var counter = 1;
    var dynamicInput = [];
    
    function addInput(){
        var newdiv = document.createElement('div');
        newdiv.id = dynamicInput[counter];
        newdiv.classList.add('row');
        newdiv.innerHTML = "<div class='col-8'> Entry "  + (counter + 1) + "<br><input type='text' class='form-control' name='requirements_description[]'> </div> <div class='col-4'> <button type='button' class='btn btn-outline-danger mt-4' onClick='removeInput("+dynamicInput[counter]+");'>Remove</button></div>";
        document.getElementById('projReq').appendChild(newdiv);
        counter++;
    }
      
      function removeInput(id){
        var elem = document.getElementById(id);
        return elem.parentNode.removeChild(elem);
      }
    </script