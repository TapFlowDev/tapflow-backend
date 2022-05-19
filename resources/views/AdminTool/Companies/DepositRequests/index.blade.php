@extends('templates.main')
@section('content')
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
                        <div class="col-8">
                            <h1 class="float-left"> {{ $company->name }} Deposit Requests</h1>
                        </div>
                        <div>

                            <a class="btn btn-sm btn-success float-right ml-2"
                                href="{{ route('AdminTool.wallet.transactions.create', $walletInfo->id) }}"
                                role="button">Add
                                Balance</a>


                            <a class="btn btn-sm btn-success float-right"
                                href="{{ route('AdminTool.wallet.transactions.index', $walletInfo->id) }}"
                                role="button">View
                                Transactions
                            </a>
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#id</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Reference Number</th>
                                <th scope="col">Date</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deposits as $deposit)
                                <tr>
                                    <th scope="row">{{ $deposit->id }}</th>
                                    <td>{{ $deposit->amount }}</td>
                                    <td>{{ $deposit->reference_number }}</td>
                                    <td>{{ date('Y-m-d', strtotime($deposit->created_at)) }}</td>
                                    <td>
                                        @if ($deposit->status < 1)
                                            Pending
                                        @elseif ($deposit->status == 1)
                                            Confirmed
                                        @else
                                            Refused
                                        @endif
                                    </td>
                                    <td>
                                        @if ($deposit->status < 1)
                                            <button class="btn btn-sm btn-success"
                                                onclick="event.preventDefault();
                                                                                        document.getElementById('withdraw-form-{{ $deposit->id }}').submit()">Confirm</button>
                                            <form id="withdraw-form-{{ $deposit->id }}"
                                                action="{{ route('AdminTool.depositRequests.update', $deposit->id) }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="1">
                                            </form>
                                            <a class="btn btn-sm btn-success"
                                                href="{{ route('AdminTool.depositRequests.edit', $deposit->id) }}"
                                                role="button">Confirm & Add Invoice</a>
                                        @elseif($deposit->status == 1)
                                            <a class="btn btn-sm btn-primary"
                                                href="{{ route('AdminTool.depositRequests.edit', $deposit->id) }}"
                                                role="button">Add Invoice</a>
                                        @endif

                                        {{-- @if ($deposit->walletId != '')
                                        <a class="btn btn-sm btn-info"
                                        href="{{ route('AdminTool.wallet.transactions.index', $deposit->walletId) }}"
                                        role="button">View Transactions</a>
                                        @else
                                            <button class="btn btn-sm btn-info" onclick="event.preventDefault();
                                            document.getElementById('create-wallet-{{ $deposit->id }}').submit()">View
                                                Transactions</button>
                                            <form id="create-wallet-{{ $deposit->id }}"
                                                action="{{ route('AdminTool.wallet.create') }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                                <input type="hidden" name="group_id" value="{{ $deposit->id }}">
                                                <input type="hidden" name="type" value="1">
                                            </form>
                                        @endif --}}
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    {{ $deposits->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
