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
                                    <img src="{{ $project->company_image }}" alt="Admin" class="rounded-circle"
                                        width="150">
                                    <div class="mt-3 team-name-link">
                                        <h4> <a class=""
                                                href="{{ route('AdminTool.agencies.show', $project->company_id) }}"
                                                role="button">{{ $project->company_name }}
                                            </a>| {{ $project->name }}</h4>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Created</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        {{ date('Y-m-d', strtotime($project->created_at)) }}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Budget</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        ${{ $project->min }} - ${{ $project->max }}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Duration</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        {{ $project->duration }}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Status</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        @if ($project->status == 0)
                                            Pending
                                        @elseif ($project->status == 1)
                                            Active
                                        @elseif ($project->status == 2)
                                            Finished
                                        @endif
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Description</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        {{ $project->description }}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0"> Requirements</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        {{ $project->requirements_description }}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Categories</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        @foreach ($project->categories as $cats)
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
