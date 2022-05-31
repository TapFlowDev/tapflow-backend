@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <h1 class="float-left">Edit Countries Scores</h1>
                    <form action="{{ route('AdminTool.countries.update', 'all') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        {{-- <a class=""
                        href="{{ route('AdminTool.countries.saveAll') }}" role="button">Save</a> --}}
                        <button type="submit" class="btn btn-sm btn-success float-right mb-3">Save</button>
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
                                        <td>
                                            <input type="number" min="0.0" max="10.0" step="0.1"
                                                name="country[{{ $country->id }}]" value="{{ $country->score }}">
                                        </td>
                                        {{-- <td>{{ $country->score }}</td> --}}
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </form>

                    {{-- {{ $priorities->links() }} --}}

                </div>
            </div>
        </div>
    </div>
@endsection
