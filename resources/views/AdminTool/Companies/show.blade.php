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
                                        <h6 class="mb-0">Admin Name</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <a class="team-name-link" href="{{ route('AdminTool.clients.show', $info->admin_id)}}" target="_blanck"
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

                                        <a class="team-name-link" href="{{ $info->link }}" target="_blanck"
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
                                        {{ $info->country_name }}
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
                                        <h6 class="mb-0">Type</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        {{ $info->field_name }}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Industry</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        {{ $info->sector_name }}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Categories</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        @foreach ($info->categories as $cats)
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
                        <div class="row">
                            <h3>Company Projects</h3>
                        </div>
                        <div class="row">
                            @foreach ($projects as $project)
                                <div class="col-lg-4">
                                    <div class="card mb-3 card-projects">
                                        <div class="card-body">
                                            <div class="row">
                                                <a href="{{ route('AdminTool.projects.show', $project->id) }}" class="project-card-href">
                                                <div class="col-sm-12 project-card">
                                                    <h6 class="mb-0">{{ $project->name }}</h6>
                                                    <p>{{ date('Y-m-d', strtotime($project->created_at)) }}</p>
                                                </div>
                                                <hr>
                                                <div class="col-sm-12 project-card">
                                                    <h6 class="mb-0">Status</h6>
                                                    <p>
                                                        @if ($project->status == 0)
                                                            Pending
                                                        @elseif ($project->status == 1)
                                                            Active
                                                        @elseif ($project->status == 2)
                                                            Finished
                                                        @endif
                                                    </p>
                                                </div>
                                            </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
