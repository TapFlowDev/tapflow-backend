@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-10 col-lg-8">
                <div class="content-container">
                    <h1>Update Agency Info:</h1>
                    <h4>Agency Name: {{ $team->name }}</h4>
                    <form action="{{ route('AdminTool.agencies.update', $team->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        @include('AdminTool.Agencies.agencyForm')
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
