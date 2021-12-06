@extends('templates.main')
@section('content')
    <h1>Edit Announcements</h1>
    <div class="row">
        <div class="col-6">
            <div class="card">
                <form action="{{ route('AdminTool.announcements.update', $announcement->id  ) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    @include('AdminTool.Announcements.adminForm')
                </form>
            </div>
        </div>
    </div>

@endsection
