@extends('templates.main')
@section('content')
    <h1>Add Admin</h1>
    <div class="row">
        <div class="col-6">
            <div class="card">
                <form action="{{ route('AdminTool.users.update', $user->id  ) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    @include('AdminTool.Users.adminForm')
                </form>
            </div>
        </div>
    </div>

@endsection
