@extends('templates.main')
@section('content')
<div class="row">
<div class="col-12">
    <h1 class="float-left"> Users </h1>
    <a class="btn btn-sm btn-success float-right mb-3" 
    href="{{ route('AdminTool.users.create') }}"
        role="button">Add</a>
</div>
</div>
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#id</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <th scope="row">{{ $user->id }}</th>
                        <td>{{ $user->first_name . ' ' . $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="{{ route('AdminTool.users.edit', $user->id) }}"
                                role="button">edit</a>
                                <button class="btn btn-sm btn-danger" 
                                    onclick="event.preventDefault();
                                    document.getElementById('delete-user-form-{{ $user->id }}').submit()">Delete</button>
                                <form id="delete-user-form-{{ $user->id }}"  action="{{ route('AdminTool.users.destroy', $user->id) }}" method="POST"
                                    style="display: none;">
                                @csrf
                                @method("DELETE")
                                </form>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    {{ $users->links() }}
    </div>

@endsection
