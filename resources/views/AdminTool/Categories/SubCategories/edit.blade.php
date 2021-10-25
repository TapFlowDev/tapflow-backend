@extends('templates.main')
@section('content')
    <h1>Add Category </h1>
    <div class="row">
        <div class="col-6">
            <div class="card">
                <form action="{{ route('AdminTool.subCategories.update', $subCategory->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    @include('AdminTool.Categories.SubCategories.categoryForm')
                </form>
            </div>
        </div>
    </div>

@endsection
