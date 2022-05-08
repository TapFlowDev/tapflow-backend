@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <h1 class="float-left"> Companies</h1>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#id</th>
                                <th scope="col">Name</th>
                                <th scope="col">Admin</th>
                                {{-- <th scope="col">is verified</th> --}}
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <th scope="row">{{ $user->id }}</th>
                                    <td>{{ $user->name }}</td>
                                    <td class="team-name-link">
                                        <a href="{{ route('AdminTool.clients.show', $user->admin_id) }}" target="_blanck"
                                            role="">{{ $user->admin_name }}
                                        </a>
                                    </td>
                                    {{-- <td>
                        @if ($user->verified == 1)
                        verified
                        @else
                        not verified
                        @endif
                    </td> --}}
                                    <td>
                                        <a class="btn btn-sm btn-primary"
                                            href="{{ route('AdminTool.companies.show', $user->id) }}"
                                            role="button">View</a>
                                        {{-- <a class="btn btn-sm btn-primary" href="{{ route('AdminTool.freelancers.show', $user->freelancer_id) }}"
                            role="button">View</a> --}}
                                        @if ($user->walletId != '')
                                            <a class="btn btn-sm btn-info"
                                                href="{{ route('AdminTool.wallet.transactions.index', $user->walletId) }}"
                                                role="button">View Transactions</a>
                                        @else
                                            <button class="btn btn-sm btn-info" onclick="event.preventDefault();
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
                                        <a class="btn btn-sm btn-outline-primary"
                                            href="{{ route('AdminTool.companies.depositRequests.index', $user->id) }}"
                                            role="button">Deposit Requests</a>
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
