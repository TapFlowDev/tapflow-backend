@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-10 col-lg-8">
                <div class="content-container">
                    <h1>Edit Category</h1>
                    <form action="{{ route('AdminTool.categories.update', $category->id) }}"
                        enctype="multipart/form-data" method="POST">
                        @csrf
                        @method('PATCH')
                        @include('AdminTool.Categories.categoryForm')
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
