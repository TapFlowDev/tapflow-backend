@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <div class="col-lg-12">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Name</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        {{ $info->name }}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Email</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        {{ $info->email }}
                                    </div>
                                </div>
                                <hr>
                                @foreach ($info->answers as $answer)
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <h6 class="mb-0">{{ $answer['question'] }}</h6>
                                        </div>
                                        <div class="col-sm-9 text-secondary">
                                            {{ $answer['answer'] }}
                                        </div>
                                    </div>
                                    @if (!$loop->last)
                                        <hr>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-lg-6">
                                <button>button</button>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
