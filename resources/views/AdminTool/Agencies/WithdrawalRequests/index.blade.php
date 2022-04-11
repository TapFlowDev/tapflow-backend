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
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @elseif (session('fail'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('fail') }}
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card mb-3">
                                @if ($billingInfo != '')
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">
                                                    Id:
                                                </h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                {{ $billingInfo->id }}
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">
                                                    Bank Name:
                                                </h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                {{ $billingInfo->bank_name }}
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">IBAN:</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                {{ $billingInfo->IBAN }}
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Country:</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                {{ $billingInfo->countryCode }}

                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>

                    <h1> Withdrawal Requests </h1>
                    <a class="btn btn-sm btn-success float-right mb-3"
                        href="{{ route('AdminTool.wallet.transactions.index', $walletInfo->id) }}" role="button">View
                        Transactions
                    </a>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#id</th>
                                <th scope="col">Type</th>
                                <th scope="col">Billing Info Id</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Date</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($withdrawlRequests as $withdrawlRequest)
                                <tr>
                                    <th scope="row">{{ $withdrawlRequest->id }}</th>
                                    <td>
                                        @if ($withdrawlRequest->type == 1)
                                            Manual Transfer
                                        @endif
                                    </td>
                                    <td>{{ $withdrawlRequest->billing_info_id }}</td>
                                    <td>{{ $withdrawlRequest->amount }}</td>
                                    <td>
                                        {{-- {{ date('Y-m-d', strtotime($withdrawlRequest->created_at)) }} --}}
                                        {{ $withdrawlRequest->created_at }}
                                    </td>
                                    <td>
                                        @if ($withdrawlRequest->status == 0)
                                            Pending
                                        @elseif ($withdrawlRequest->status == 1)
                                            Success
                                        @elseif ($withdrawlRequest->status == 2)
                                            Fail
                                        @endif
                                    </td>
                                    <td>
                                        @if ($withdrawlRequest->status == 0)
                                            <button class="btn btn-sm btn-primary"
                                                onclick="event.preventDefault();
                                                                    document.getElementById('withdraw-form-{{ $withdrawlRequest->id }}').submit()">Withdraw</button>
                                            <form id="withdraw-form-{{ $withdrawlRequest->id }}"
                                                action="{{ route('AdminTool.agencies.withdrawal.update', [$withdrawlRequest->group_id, $withdrawlRequest->id]) }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="wallet" value="{{ $walletInfo->id }}">
                                                <input type="hidden" name="amount"
                                                    value="{{ $withdrawlRequest->amount }}">
                                            </form>
                                        @endif

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    {{ $withdrawlRequests->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
