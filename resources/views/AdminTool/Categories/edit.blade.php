@extends('templates.main')
@section('content')
    <h1>Add Admin</h1>
    <div class="row">
        <div class="col-6">
            <div class="card">
                <form action="{{ route('AdminTool.categories.update', $category->id  ) }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    @method('PATCH')
                    @include('AdminTool.Categories.categoryForm')
                </form>
            </div>
        </div>
    </div>

@endsection
