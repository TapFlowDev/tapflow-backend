@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-10 col-lg-8">
                <div class="content-container">
                    <h1>Add Options</h1>
                    <form action="{{ route('AdminTool.notificationSettings.store') }}" method="POST" >
                        @csrf
                        @include('AdminTool.NotificationsSettings.notificationSettingsForm')
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
