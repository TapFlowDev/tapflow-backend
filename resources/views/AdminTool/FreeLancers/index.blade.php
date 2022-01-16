@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <h1> Agency Members</h1>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#id</th>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Team</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <th scope="row">{{ $user->id }}</th>
                                    <td>{{ $user->first_name . ' ' . $user->last_name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td class="team-name-link">
                                        <a class=""
                                            href="{{ route('AdminTool.agencies.show', $user->team_id) }}"
                                            role="button">{{ $user->team_name }}
                                        </a>
                                        @if ($user->group_verfied==1)
                                        <i class="fas fa-check"></i>
                                        @else                       
                                        <i class="fas fa-times"></i>                     
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-primary"
                                            href="{{ route('AdminTool.freelancers.show', $user->id) }}"
                                            role="button">View</a>
                                        <a class="btn btn-sm btn-primary"
                                            href="{{ route('AdminTool.freelancers.sendEmailShow', $user->id) }}"
                                            role="button">Send Email</a>
                                        <button class="btn btn-sm btn-danger"
                                            onclick="event.preventDefault();
                                                document.getElementById('delete-user-form-{{ $user->id }}').submit()">Block</button>
                                        <form id="delete-user-form-{{ $user->id }}"
                                            action="{{ route('AdminTool.freelancers.destroy', $user->id) }}" method="POST"
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
            </div>

        </div>
    </div>
@endsection
