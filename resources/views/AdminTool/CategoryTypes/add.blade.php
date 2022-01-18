@extends('templates.main')
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-10 col-lg-8">
                <div class="content-container">
                    <h1>Add Category Type</h1>
                    <form action="{{ route('AdminTool.categoryTypes.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @include('AdminTool.CategoryTypes.categoryForm')
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
