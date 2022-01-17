@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-10 col-lg-8">
                <div class="content-container">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @elseif (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                    @endif
                    <h1>Send Email to</h1>
                    <h3>{{ $user->email }}</h3>
                    <form action="{{ route('AdminTool.sendEmail.send') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" name="subject" required class="form-control" id="subject"
                                aria-describedby="emailHelp" value="{{ old('subject') }}">
                            @error('subject')
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea name="content" class="ckeditor form-control" class="form-control" id="content"
                                aria-describedby="emailHelp">{{ old('content') }}</textarea>
                            @error('content')
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
