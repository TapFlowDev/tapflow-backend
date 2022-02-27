@extends('templates.main')
@section('content')
    <div class="cardBox">
        <div class="row">
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div>
                        <div class="numbers">{{ $stats['agency'] }}</div>
                        <div class="cardName">Agencies</div>
                    </div>
                    <div class="iconBox">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div>
                        <div class="numbers">{{ $stats['company'] }}</div>
                        <div class="cardName">Clinets</div>
                    </div>
                    <div class="iconBox">
                        <i class="fas fa-briefcase"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div>
                        <div class="numbers">{{ $stats['projectRequests'] }}</div>
                        <div class="cardName">Project Requests</div>
                    </div>
                    <div class="iconBox">
                        <i class="far fa-folder-open"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div>
                        <div class="numbers">{{ $stats['project'] }}</div>
                        <div class="cardName">Finished Projects</div>
                    </div>
                    <div class="iconBox">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="details">
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="recentAgencies">
                    <div class="cardHeader">
                        <h2>Unverified Agencies</h2>
                        <a href="/AdminTool/agencies" class="view-all-btn">View All</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <td>Name</td>
                                <td>Link</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($teams as $team)
                                <tr>
                                    <td>{{ $team->name }}</td>
                                    <td class="team-name-link">
                                        <a href="{{ $team->link }}">{{ $team->link }}</a>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success"
                                            onclick="event.preventDefault();
                                                                    document.getElementById('verifyTeam-user-form-{{ $team->id }}').submit()">Verify</button>
                                        <form id="verifyTeam-user-form-{{ $team->id }}"
                                            action="{{ route('AdminTool.group.update', $team->id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="verify" value="1">
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="recentAgencies">
                    <div class="cardHeader">
                        <h2>Othes Users</h2>
                        <a href="/AdminTool/users" class="view-all-btn">View All</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <td>Name</td>
                                <td>Email</td>
                                <td>Type</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->first_name . ' ' . $user->last_name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($user->type == 1)
                                            Agency Member
                                        @else
                                            Client
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-secondary mb-1"
                                            href="{{ route('AdminTool.sendEmailShow.show', $user->id) }}"
                                            role="button">Send Email</a>
                                        <a class="btn btn-sm btn-primary mb-1"
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


                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
