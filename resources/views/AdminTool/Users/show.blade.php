@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <div class="col-lg-12">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex flex-column align-items-center text-center">
                                    <img src="{{ $info->image }}" alt="Admin" class="rounded-circle" width="150">
                                    <div class="mt-3">
                                        <h4>{{ $info->full_name }}</h4>
                                    </div>

                                </div>
                                {{-- <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Agency Name</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary team-name-link">
                                        <a class=""
                                            href="{{ route('AdminTool.agencies.show', $info->team_id) }}"
                                            role="button">{{ $info->team_name }}
                                        </a>
                                        @if ($info->group_verfied==1)
                                        <i class="fas fa-check"></i>
                                        @else                       
                                        <i class="fas fa-times"></i>                     
                                        @endif
                                    </div>
                                </div> --}}
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
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Role</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        {{ $info->role }}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Country</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        {{ $info->country }}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Bio</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        {{ $info->bio }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
