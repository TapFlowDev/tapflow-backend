@extends('templates.main')
@section('content')
    <h1>Add Admin</h1>
    <div class="row">
        <div class="col-6">
            <div class="card">
                <form action="{{ route('AdminTool.announcements.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('AdminTool.Announcements.adminForm')
                </form>
            </div>
        </div>
    </div>

@endsection
