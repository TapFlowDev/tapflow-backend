@extends('layouts.app')

@section('content')
<div>
    <p>tapflow chat</p>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <button onclick="startFCM()" class="btn btn-danger btn-flat">Allow notification
            </button>

            <div class="card mt-3">
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <form action="{{'send-notification' }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Message Title</label>
                            <input type="text" class="form-control" name="title">
                        </div>
                        <div class="form-group">
                            <label>Message Body</label>
                            <textarea class="form-control" name="body"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Send Notification</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase.js"></script>

<script>
    var firebaseConfig = {
        apiKey: "AIzaSyBlQKDh6sFqBhCBkvBpeuDvbiIJ8mux3oU",
        authDomain: "laravel-firbase22.firebaseapp.com",
        projectId: "laravel-firbase22",
        storageBucket: "laravel-firbase22.appspot.com",
        messagingSenderId: "944849382948",
        appId: "1:944849382948:web:8d1a3e549833a7898b72b1",
        measurementId: "G-Z8XFTGKMDK"
    };

    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    function startFCM() {
        messaging
            .requestPermission()
            .then(function() {
               
                return messaging.getToken()
            })
            .then(function(response) {
                console.log(response);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{ route("store.token") }}',
                    type: 'POST',
                    data: {
                        token: response
                    },
                    dataType: 'JSON',
                    success: function(response) {
                        alert('Token stored.');
                    },
                    error: function(error) {
                        alert(error);
                    },
                });

            }).catch(function(error) {
                alert(error);
            });
    }

    messaging.onMessage(function(payload) {
        const title = payload.notification.title;
        const options = {
            body: payload.notification.body,
            icon: payload.notification.icon,
        };
        new Notification(title, options);
    });
</script>
@endsection