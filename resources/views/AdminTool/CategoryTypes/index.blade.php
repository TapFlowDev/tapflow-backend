@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <h1> Category Types </h1>
                    <a class="btn btn-sm btn-success float-right mb-3" href="{{ route('AdminTool.categoryTypes.create') }}"
                        role="button">Add</a>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#id</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $categoryType)
                                    <tr>
                                        <th scope="row">{{ $categoryType->id }}</th>
                                        <td>{{ $categoryType->name }}</td>
                                        <td>
                                            <a class="btn btn-sm btn-primary"
                                                href="{{ route('AdminTool.categoryTypes.edit', $categoryType->id) }}" role="button">edit</a>
                                            <a class="btn btn-sm btn-secondary"
                                                href="{{ route('AdminTool.categoryTypes.categories.index', $categoryType->id) }}"
                                                role="button">Categories</a>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
            
                            </tbody>
                        </table>
                        {{ $categories->links() }}
                </div> 
            </div>
        </div>
    </div>

@endsection
