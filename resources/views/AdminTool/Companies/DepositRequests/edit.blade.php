@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-10 col-lg-8">
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
                    <h1>Confirm Deposit:</h1>
                    <h4>Company Name: {{ $company->name }}</h4>
                    <form action="{{ route('AdminTool.depositRequests.update', $deposit->id) }}" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="status" value="1">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="formFile" class="form-label">Insert Invoive</label>
                            <input class="form-control" name="invoice" type="file" id="formFile">
                            @isset($deposit->invoice)
                                <a class="" style="color:black" target="_blanck" href="{{ asset('images/invoices/' . $deposit->invoice) }}" role="">View
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
