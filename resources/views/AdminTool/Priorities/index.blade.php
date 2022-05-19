@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <h1 class="float-left">Priorities</h1>
                    <a class="btn btn-sm btn-success float-right mb-3"
                        href="{{ route('AdminTool.priorities.create') }}" role="button">Add</a>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#id</th>
                                <th scope="col">Name</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($priorities as $priority)
                                <tr>
                                    <th scope="row">{{ $priority->id }}</th>
                                    <td>{{ $priority->name }}</td>
                                    <td>
                                        <a class="btn btn-sm btn-primary"
                                            href="{{ route('AdminTool.priorities.edit', $priority->id) }}"
                                            role="button">edit</a>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    {{ $priorities->links() }}

                </div>
            </div>
        </div>
    </div>

@endsection
