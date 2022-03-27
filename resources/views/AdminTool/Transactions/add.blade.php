@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-10 col-lg-8">
                <div class="content-container">
                    <h1>Add Balance</h1>
                    <h4>Current Balance: {{ $current }}</h4>
                    <form action="{{ route('AdminTool.wallet.transactions.store', $wallet) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="wallet" value="{{ $wallet }}">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">Amount in USD:</label>
                            <input type="number" min="0.01" step="any" name="amount" required class="form-control" id="amount"
                                aria-describedby="emailHelp"
                                value="{{ old('amount') }}">
                            @error('amount')
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Add Balance</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
