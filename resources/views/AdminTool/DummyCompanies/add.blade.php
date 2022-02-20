@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <h1>Add Dummy Company</h1>
                    <div class="row">
                        <div class="col-6">
                            <form action="{{ route('AdminTool.dummyCompanies.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @include('AdminTool.DummyCompanies.dummyCompaniesForm')
                            </form>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection
