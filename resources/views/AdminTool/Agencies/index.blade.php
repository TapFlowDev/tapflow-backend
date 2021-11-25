@extends('templates.main')
@section('content')
    <div class="row">
        <div class="col-12">
            <h1 class="float-left"> Teams/Agencies</h1>
        </div>
    </div>
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#id</th>
                    <th scope="col">Name</th>
                    <th scope="col">is verified</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <th scope="row">{{ $user->id }}</th>
                        <td>{{ $user->name }}</td>
                        <td>
                            @if ($user->verified == 1)
                                verified
                            @else
                                not verified
                            @endif
                        </td>
                        <td>
                            {{-- <a class="btn btn-sm btn-primary" href="{{ route('AdminTool.freelancers.show', $user->freelancer_id) }}"
                                role="button">View</a> --}}
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
                            @elseif ($user->verified==1)
                                <button class="btn btn-sm btn-danger"
                                    onclick="event.preventDefault();
                                    document.getElementById('verifyTeam-user-form-{{ $user->id }}').submit()">Unverify</button>
                                <form id="verifyTeam-user-form-{{ $user->id }}"
                                    action="{{ route('AdminTool.Group.update', $user->id) }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="verify" value="0">

                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
        {{ $users->links() }}
    </div>

@endsection
