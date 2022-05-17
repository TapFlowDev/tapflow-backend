@extends('templates.main')
@section('content')
    <div class="container">
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
            <div class="col-10 col-lg-8">
                <div class="content-container">
                    <h1>Submit Withdrawal:</h1>
                    <h4>Agency Name: {{ $team->name }}</h4>
                    <form action="{{ route('AdminTool.agencies.withdrawal.update', [$team->id, $withdrawal->id]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="wallet" value="{{ $wallet }}">
                        <input type="hidden" name="amount" value="{{ $withdrawal->amount }}">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount in USD:</label>
                            <input type="text" name="amountText" required disabled class="form-control" id="amount"
                                aria-describedby="emailHelp" value="{{ $withdrawal->amount }}">
                            @error('amount')
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="formFile" class="form-label">Insert Invoive</label>
                            <input class="form-control" name="invoice" type="file" id="formFile">
                            @isset($withdrawal->invoice)
                                <a class="" style="color:black" target="_blanck"
                                    href="{{ asset('images/invoices/' . $withdrawal->invoice) }}" role="">View
                                    Invoice</a>
                            @endisset
                            @error('invoice')
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
