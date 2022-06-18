@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-10 col-lg-8">
                <div class="content-container">
                    <h1>Add Priority </h1>
                    <form action="{{ route('AdminTool.priorities.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @include('AdminTool.Priorities.prioritiesForm')
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
