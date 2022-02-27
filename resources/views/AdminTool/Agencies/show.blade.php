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
                                        <h4>{{ $info->name }}</h4>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Verified</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary verify">
                                        @if ($info->verified == 0)
                                        <i class="fas fa-times"></i>
                                            <button class="btn btn-sm btn-success"
                                                onclick="event.preventDefault();
                                                    document.getElementById('verifyTeam-user-form-{{ $info->id }}').submit()">Verify</button>
                                            <form id="verifyTeam-user-form-{{ $info->id }}"
                                                action="{{ route('AdminTool.group.update', $info->id) }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="verify" value="1">

                                            </form>
                                        @elseif ($info->verified==1)
                                        <i class="fas fa-check"></i>
                                            <button class="btn btn-sm btn-danger"
                                                onclick="event.preventDefault();
                                                document.getElementById('verifyTeam-user-form-{{ $info->id }}').submit()">Unverify</button>
                                            <form id="verifyTeam-user-form-{{ $info->id }}"
                                                action="{{ route('AdminTool.group.update', $info->id) }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="verify" value="0">

                                            </form>
                                        @endif
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Admin Name</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <a class="team-name-link" href="{{ route('AdminTool.freelancers.show', $info->admin_id)}}" target="_blanck"
                                            role="button">{{ $info->admin_name }}
                                        </a>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Admin Email</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">

                                        <a class="team-name-link" href="mailto:{{ $info->admin_email }}" target="_blanck"
                                            role="button">{{ $info->admin_email }}
                                        </a>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Link</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        
                                        <a class="team-name-link"
                                            href="{{ $info->link }}"
                                            target="_blanck"
                                            role="button">{{ $info->link }}
                                        </a>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Employees Number</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        {{ $info->employees_number }}
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
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Categories</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        @foreach ($info->categories as $cats )
                                           {{ $cats['name'] }}:
                                           @foreach ($cats['subs'] as $subs)
                                             {{ $subs->name }}
                                           @if (!$loop->last)
                                             ,
                                           @endif
                                           @endforeach 
                                           @if (!$loop->last)
                                               <hr>
                                           @endif
                                        @endforeach
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
