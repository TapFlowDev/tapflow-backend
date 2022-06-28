@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-10 col-lg-8">
                <div class="content-container">
                    <h1>Add features</h1>
                    <form id="addForm" action="{{ route('AdminTool.features.store') }}" method="POST">
                        @csrf
                        <div id="hidden-inputs" style="display: none;">
                            {{-- <input type="hidden" name="skill[]">  --}}
                        </div>
                        <div class="mb-3" id="projReq">
                            <div class="row">
                                <div class="col-8">
                                    <label for="skillName">Enter feature:</label> <input type="text" class="form-control"
                                        id='skillName'>
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn btn-outline-success mt-4"
                                        onClick="addInput();">Add</button>
                                </div>
                            </div>
                            <div>
                                <label class="text-danger d-none" id='err-label'> feature already exists !</label>
                            </div>
                            @error('feature')
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <button type="button" id="submitButton" onClick="submitForm();"
                            class="btn btn-primary">Submit</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        var counter = 1;
        var dynamicInput = [];
        const allInputs = [];
        const skillName = document.getElementById('skillName');
        const hiddenDiv = document.getElementById('hidden-inputs');
        skillName.addEventListener("keypress", function(event) {
            // If the user presses the "Enter" key on the keyboard
            if (event.key === "Enter") {
                // Cancel the default action, if needed
                addInput();
                event.preventDefault();
            }
        })

        function addInput() {
            var newdiv = document.createElement('div');
            var skillNameInput = document.getElementById('skillName').value;
            var success = 0;
            var skillTrimed = '';
            checkIfExists(skillNameInput).then(function(data) {
                // Run this when your request was successful
                skillTrimed = data.data.trimed;
                // console.log('skill: ', skillTrimed);
                // console.log('includes: ', !allInputs.includes(skillTrimed));
                if (data.status === 200 && !allInputs.includes(skillTrimed)) {
                    console.log('data trim: ', skillTrimed)
                    newdiv.id = counter;
                    newdiv.classList.add('row');
                    newdiv.classList.add('mt-2');
                    newdiv.innerHTML = "<div class='col-8'> Entry " + (counter) +
                        "<br><input type='text' class='form-control' disabled name='features[]' value='" +
                        skillNameInput +
                        "'></div> <div class='col-4'> <button type='button' class='btn btn-outline-danger mt-4' onClick='removeInput(" +
                        counter + ");'>Remove</button></div>";
                    document.getElementById('projReq').appendChild(newdiv);
                    counter++;
                    allInputs.push(skillTrimed);
                    console.log(allInputs);
                    document.getElementById('skillName').value = "";
                    document.getElementById('err-label').classList.add('d-none');
                    var hiddenHtml = `<input type="hidden" name="feature[]" value='${skillNameInput}'>`;
                    hiddenDiv.innerHTML += hiddenHtml;

                } else {
                    document.getElementById('err-label').classList.remove('d-none');
                    console.log('err');

                }
            }).catch(function(err) {
                // Run this when promise was rejected via reject()
                console.log('err: ', err)
            })


        }

        function removeInput(id) {
            var elem = document.getElementById(id);
            return elem.parentNode.removeChild(elem);
        }

        function checkIfExists(skill) {
            var success = 0;
            var skillTrimed = '';
            return $.ajax({
                url: '/AdminTool/checkFeature/' + skill,
                type: 'get',
                dataType: 'JSON',
                data: {},
                success: function(response) {
                    // $("iname").value = data.info.imei_number;
                    // console.log(response);
                    success = 1;
                    skillTrimed = response.data.trimed;
                    // console.log('df', skillTrimed);
                    // return {
                    //     'success': success,
                    //     'skill': skillTrimed
                    // }
                },
                error: function(response) {
                    console.log(response);
                }
            });

        }

        function submitForm() {
            
            document.getElementById('addForm').submit();
            // console.log(formData);
        }
    </script>
@endsection
