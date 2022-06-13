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
                    <h1>Add features</h1>
                    <form id="addForm" action="{{ route('AdminTool.features.creatAddByCSV') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="mb-3">
                            <label for="formFile" class="form-label">Insert Image Agency</label>
                            <input class="form-control" name="CSV" type="file" id="formFile">
                            @error('CSV')
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <button type="submit" id="submitButton" 
                            class="btn btn-primary">Submit</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
