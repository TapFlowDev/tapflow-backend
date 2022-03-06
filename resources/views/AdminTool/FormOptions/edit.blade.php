@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-10 col-lg-8">
                <div class="content-container">
                    <h1>Edit Options</h1>
                    <form action="{{ route('AdminTool.formOptions.update', $option->id) }}"
                        enctype="multipart/form-data" method="POST">
                        @csrf
                        @method('PATCH')
                        @include('AdminTool.FormOptions.formOptionsForm')
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
