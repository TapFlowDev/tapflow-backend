@extends('templates.main')
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-10 col-lg-8">
                <div class="content-container">
                    <h1>Add Options</h1>
                    <form action="{{ route('AdminTool.formOptions.store') }}" method="POST" >
                        @csrf
                        @include('AdminTool.FormOptions.formOptionsForm')
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
