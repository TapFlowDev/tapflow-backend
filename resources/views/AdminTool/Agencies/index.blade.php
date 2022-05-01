@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <h1> Agencies</h1>
                    <a class="btn btn-sm btn-success float-right mb-3" target="_blank"
                        href="{{ route('AdminTool.agecies.exportCsv') }}" role="button">Export to CSV</a>

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
                                        @if ($user->walletId != '')
                                            <a class="btn btn-sm btn-info"
                                                href="{{ route('AdminTool.wallet.transactions.index', $user->walletId) }}"
                                                role="button">View Transactions</a>
                                        @else
                                            <button class="btn btn-sm btn-info"
                                                onclick="event.preventDefault();
                                                        document.getElementById('create-wallet-{{ $user->id }}').submit()">View
                                                Transactions</button>
                                            <form id="create-wallet-{{ $user->id }}"
                                                action="{{ route('AdminTool.wallet.create') }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                                <input type="hidden" name="group_id" value="{{ $user->id }}">
                                                <input type="hidden" name="type" value="1">
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
                                        <a class="btn btn-sm btn-outline-info"
                                            href="{{ route('AdminTool.agencies.edit', $user->id) }}" role="button">Update
                                            Info</a>
                                        <a class="btn btn-sm btn-outline-info"
                                            href="{{ route('AdminTool.agencies.withdrawal.index', $user->id) }}" role="button">Withdrawal Requests</a>
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
