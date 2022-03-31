@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <h1 class="float-left"> {{ $company->name }} Deposit Requests</h1>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#id</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Reference Number</th>
                                <th scope="col">Date</th>
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
                                        {{-- <a class="btn btn-sm btn-primary"
                                            href="{{ route('AdminTool.companies.show', $deposit->id) }}"
                                            role="button">View</a>
                                        @if ($deposit->walletId != '')
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
