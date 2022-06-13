@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @elseif (session('fail'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('fail') }}
                        </div>
                    @endif
                    <h1> Skills </h1>
                    <a class="btn btn-sm btn-success float-right mb-3"
                        href="{{ route('AdminTool.skills.create') }}"
                        role="button">Add</a>
                        <a class="btn btn-sm btn-success float-right mb-3"
                        href="{{ route('AdminTool.skills.showAddByCSV') }}"
                        role="button">Add by CSV</a>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#id</th>
                                <th scope="col">Name</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($skills as $skill)
                                <tr>
                                    <th scope="row">{{ $skill->id }}</th>
                                    <td>{{ $skill->name }}</td>
                                    <td>
                                        <a class="btn btn-sm btn-primary"
                                            href="{{ route('AdminTool.skills.edit', $skill->id) }}"
                                            role="button">edit</a>
                                        <button
                                            class="btn btn-sm 
                                            @if ($skill->deleted == 1) btn-success 
                                             @else
                                             btn-danger @endif
                                             "
                                            onclick="event.preventDefault();
                                                            document.getElementById('delete-cat-form-{{ $skill->id }}').submit()">
                                            @if ($skill->deleted == 1)
                                            Show
                                            @else
                                            Hide
                                            @endif
                                        </button>
                                        <form id="delete-cat-form-{{ $skill->id }}"
                                            action="{{ route('AdminTool.skills.destroy', $skill->id) }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    {{ $skills->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
