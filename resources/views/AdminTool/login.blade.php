@extends('templates.main')
@section('content')
    <h1>Login</h1>
    <div class="row">
        <div class="col-6">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control" id="exampleInputEmail1"
                        aria-describedby="emailHelp" value="{{ old('email') }}">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="exampleInputPassword1">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                {{-- <input type="email" name='email' placeholder="email" value="{{ old('email') }}"><br><br>
        <input type="password" name='password' placeholder="password"><br><br>
        <button type="submit"> login </button> --}}

            </form>
        </div>
    </div>

@endsection
