@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-10 col-lg-8">
                <div class="content-container">
                    <h1>Edit Announcements</h1>
                    <form action="{{ route('AdminTool.announcements.update', $announcement->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        @include('AdminTool.Announcements.adminForm')
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
