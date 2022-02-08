@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <h1> Waiting List </h1>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $keyUser => $user)
                                <tr>
                                    <th scope="row">{{ $keyUser+1 }}</th>
                                    <td>{{ $user->email }}</td>
                                    {{-- <td>
                                        <a class="btn btn-sm btn-secondary"
                                            href="{{ route('AdminTool.sendEmailShow.show', $user->id) }}"
                                            role="button">Send Email</a>
                                        <a class="btn btn-sm btn-primary"
                                            href="{{ route('AdminTool.users.show', $user->id) }}" role="button">View</a>
                                        <button class="btn btn-sm btn-danger"
                                            onclick="event.preventDefault();
                                                document.getElementById('delete-user-form-{{ $user->id }}').submit()">Delete</button>
                                        <form id="delete-user-form-{{ $user->id }}"
                                            action="{{ route('AdminTool.users.destroy', $user->id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method("DELETE")
                                        </form>
                                    </td> --}}
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
