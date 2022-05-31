@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <h1 class="float-left">Countries</h1>
                    <a class="btn btn-sm btn-success float-right mb-3"
                        href="{{ route('AdminTool.countries.edit', 'all') }}" role="button">Edit</a>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#id</th>
                                <th scope="col">Name</th>
                                <th scope="col">Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($countries as $country)
                                <tr>
                                    <th scope="row">{{ $country->id }}</th>
                                    <td>{{ $country->name }}</td>
                                    <td>{{ $country->score }}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    {{-- {{ $priorities->links() }} --}}

                </div>
            </div>
        </div>
    </div>

@endsection
