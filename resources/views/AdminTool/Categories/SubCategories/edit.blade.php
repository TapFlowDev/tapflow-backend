@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-10 col-lg-8">
                <div class="content-container">
                    <h1>Add Sub Category </h1>
                    <form action="{{ route('AdminTool.subCategories.update', $subCategory->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        @include('AdminTool.Categories.SubCategories.categoryForm')
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
