@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <h1>Add Dummy Project</h1>
                    <div class="row">
                        <div class="col-6">
                            <form action="{{ route('AdminTool.projects.store') }}" method="POST">
                                @csrf
                                @include('AdminTool.Projects.projectForm')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
