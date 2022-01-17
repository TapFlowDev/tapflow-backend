@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <h1> Clients </h1>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#id</th>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Company</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <th scope="row">{{ $user->id }}</th>
                                    <td>{{ $user->first_name . ' ' . $user->last_name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->team_name }}</td>
                                    <td>
                                        <a class="btn btn-sm btn-primary"
                                            href="{{ route('AdminTool.clients.show', $user->id) }}"
                                            role="button">View</a>
                                        {{-- <button class="btn btn-sm btn-danger" 
                                onclick="event.preventDefault();
                                document.getElementById('delete-user-form-{{ $user->id }}').submit()">Delete</button>
                            <form id="delete-user-form-{{ $user->id }}"  action="{{ route('AdminTool.users.destroy', $user->id) }}" method="POST"
                                style="display: none;">
                            @csrf
                            @method("DELETE")
                            </form> --}}
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
