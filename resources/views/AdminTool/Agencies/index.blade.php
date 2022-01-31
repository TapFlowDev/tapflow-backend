@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <h1> Agencies</h1>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#id</th>
                                <th scope="col">Name</th>
                                <th scope="col">Link</th>
                                <th scope="col">Admin</th>
                                <th scope="col">is verified</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <th scope="row">{{ $user->id }}</th>
                                    <td>{{ $user->name }}</td>
                                    <td class="team-name-link">
                                        <a href="{{ $user->link }}" target="_blanck" role="">{{ $user->link }}
                                        </a>
                                    </td>
                                    <td class="team-name-link">
                                        <a href="{{ route('AdminTool.freelancers.show', $user->admin_id) }}"
                                            target="_blanck" role="">{{ $user->admin_name }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($user->verified == 1)
                                            <i class="fas fa-check"></i>
                                        @else
                                            <i class="fas fa-times"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-primary"
                                            href="{{ route('AdminTool.agencies.show', $user->id) }}"
                                            role="button">View</a>
                                        @if ($user->verified == 0)
                                            <button class="btn btn-sm btn-success"
                                                onclick="event.preventDefault();
                                                            document.getElementById('verifyTeam-user-form-{{ $user->id }}').submit()">Verify</button>
                                            <form id="verifyTeam-user-form-{{ $user->id }}"
                                                action="{{ route('AdminTool.group.update', $user->id) }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="verify" value="1">

                                            </form>
                                        @elseif ($user->verified == 1)
                                            <button class="btn btn-sm btn-danger"
                                                onclick="event.preventDefault();
                                                        document.getElementById('verifyTeam-user-form-{{ $user->id }}').submit()">Unverify</button>
                                            <form id="verifyTeam-user-form-{{ $user->id }}"
                                                action="{{ route('AdminTool.group.update', $user->id) }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="verify" value="0">

                                            </form>
                                        @endif
                                        <button class="btn btn-sm btn-danger"
                                            onclick="event.preventDefault();
                                        document.getElementById('delete-user-form-{{ $user->id }}').submit()">Delete</button>
                                        <form id="delete-user-form-{{ $user->id }}"
                                            action="{{ route('AdminTool.agencies.destroy', $user->id) }}" method="POST"
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
