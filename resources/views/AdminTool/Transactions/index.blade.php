@extends('templates.main')
@section('content')
    <div class="cardBox">
        <div class="row">
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div>
                        <div class="numbers">{{ $walletInfo->balance }}</div>
                        <div class="cardName">Balance</div>
                    </div>
                    <div class="iconBox">
                        <i class="bi bi-wallet"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">
                                        @if ($groupInfo['group_type'] == 1)
                                            Agency Name:
                                        @else
                                            Company Name:
                                        @endif
                                    </h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    @if ($groupInfo['group_type'] == 1)
                                        <a class="team-name-link"
                                            href="{{ route('AdminTool.agencies.show', $groupInfo['group_id']) }}"
                                            target="_blanck" role="button">{{ $groupInfo['group_name'] }}
                                        </a>
                                    @else
                                        <a class="team-name-link"
                                            href="{{ route('AdminTool.companies.show', $groupInfo['group_id']) }}"
                                            target="_blanck" role="button">{{ $groupInfo['group_name'] }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Admin Name:</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    @if ($groupInfo['group_type'] == 1)
                                        <a class="team-name-link"
                                            href="{{ route('AdminTool.freelancers.show', $groupInfo['admin_id']) }}"
                                            target="_blanck" role="button">{{ $groupInfo['admin_name'] }}
                                        </a>
                                    @else
                                        <a class="team-name-link"
                                            href="{{ route('AdminTool.clients.show', $groupInfo['admin_id']) }}"
                                            target="_blanck" role="button">{{ $groupInfo['admin_name'] }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Admin Email:</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <a class="team-name-link" href="mailto:{{ $groupInfo['admin_email'] }}"
                                        target="_blanck" role="button">{{ $groupInfo['admin_email'] }}
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                    <h1> Transactions </h1>
                    @if ($groupInfo['group_type'] == 2)
                        <a class="btn btn-sm btn-success float-right mb-3"
                            href="{{ route('AdminTool.wallet.transactions.create', $walletInfo->id) }}" role="button">Add
                            Balance</a>
                    @endif
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#id</th>
                                <th scope="col">Transaction Type</th>
                                <th scope="col">Ammount</th>
                                <th scope="col">Date</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $transation)
                                <tr>
                                    <th scope="row">{{ $transation->id }}</th>
                                    <td>
                                        @if ($transation->type == 1)
                                            Depost
                                        @elseif ($transation->type == 2)
                                            Withdraw
                                        @endif
                                    </td>
                                    <td>{{ $transation->amount }}</td>
                                    <td>
                                        {{-- {{ date('Y-m-d', strtotime($transation->created_at)) }} --}}
                                        {{ $transation->created_at }}
                                    </td>
                                    <td>
                                        @if ($transation->status == 0)
                                            Failed
                                        @elseif ($transation->status == 1)
                                            Succeeded
                                        @endif
                                    </td>
                                    <td>
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
