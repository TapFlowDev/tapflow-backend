@extends('templates.main')
@section('content')
<div class="row">
<div class="col-12">
    <h1 class="float-left"> Categories </h1>
    <a class="btn btn-sm btn-success float-right mb-3" 
    href="{{ route('AdminTool.categories.create') }}"
        role="button">Add</a>
</div>
</div>
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#id</th>
                    <th scope="col">Name</th>
                    <th scope="col">type</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <th scope="row">{{ $category->id }}</th>
                        <td>{{ $category->name}}</td>
                        <td>{{ $category->type }}</td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="{{ route('AdminTool.categories.edit', $category->id) }}"
                                role="button">edit</a>
                                <a class="btn btn-sm btn-secondary" href="{{ route('AdminTool.categories.subCategories.index', $category->id) }}"
                                role="button">Manage Sub Categories</a>
                                <button class="btn btn-sm btn-danger" 
                                    onclick="event.preventDefault();
                                    document.getElementById('delete-cat-form-{{ $category->id }}').submit()">Delete</button>
                                <form id="delete-cat-form-{{ $category->id }}"  action="{{ route('AdminTool.categories.destroy', $category->id) }}" method="POST"
                                    style="display: none;">
                                @csrf
                                @method("DELETE")
                                </form>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    {{ $categories->links() }}
    </div>

@endsection
