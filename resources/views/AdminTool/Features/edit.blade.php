@extends('templates.main')
@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @elseif (session('fail'))
            <div class="alert alert-danger" role="alert">
                {{ session('fail') }}
            </div>
        @endif
        <div class="row">
            <div class="col-10 col-lg-8">
                <div class="content-container">
                    <h1>Edit feature</h1>
                    <form id="addForm" action="{{ route('AdminTool.features.update', $feature->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="id" value="{{ $feature->id }}">
                        <div class="mb-3">
                            <label for="name" class="form-label">feature</label>
                            <input type="text" name="name" required class="form-control" id="name"
                                aria-describedby="emailHelp"
                                value="{{ old('name') }}@isset($feature){{ $feature->name }}@endisset">
                            @error('name')
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <button type="button" id="submitButton" class="btn btn-primary" onClick="submitForm();"
                            disabled>Submit</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        const skillName = document.getElementById('name').value;
        const nameInput = document.getElementById('name');
        nameInput.addEventListener('input', function(e) {
            if (this.value != skillName) {
                document.getElementById('submitButton').disabled = false;
            }else{
                document.getElementById('submitButton').disabled = true;

            }
        });

        function submitForm() {

            document.getElementById('addForm').submit();
            //console.log(skillName);
        }
    </script>
@endsection
