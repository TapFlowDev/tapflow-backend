@extends('templates.main')
@section('content')
    <h1>Add Category</h1>
    <div class="row">
        <div class="col-6">
            <div class="card">
                <form action="{{ route('AdminTool.categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('AdminTool.Categories.categoryForm')
                </form>
            </div>
        </div>
    </div>

@endsection
