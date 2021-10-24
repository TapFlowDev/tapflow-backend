@extends('templates.main')
@section('content')
    <h1>Add Category </h1>
    <div class="row">
        <div class="col-6">
            <div class="card">
                <form action="{{ route('AdminTool.categories.subCategories.store', $categoryId) }}" method="POST">
                    @csrf
                    @include('AdminTool.Categories.SubCategories.categoryForm')
                </form>
            </div>
        </div>
    </div>

@endsection
