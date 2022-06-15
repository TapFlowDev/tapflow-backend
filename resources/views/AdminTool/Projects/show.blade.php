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
                                                href="{{ route('AdminTool.companies.show', $project->company_id) }}"
                                                role="button">{{ $project->company_name }}
                                            </a>| {{ $project->name }}</h4>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Admin Name</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <a class="team-name-link"
                                            href="{{ route('AdminTool.clients.show', $project->admin_id) }}"
                                            target="_blanck" role="button">{{ $project->admin_name }}
                                        </a>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Admin Email</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">

                                        <a class="team-name-link" href="mailto:{{ $project->admin_email }}"
                                            target="_blanck" role="button">{{ $project->admin_email }}
                                        </a>
                                    </div>
                                </div>
                                <hr>
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
                                        @foreach ($project->requirments_description as $requirment)
                                            {{ $requirment }} @if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Categories</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        @foreach ($project->categories as $cats)
                                            {{ $cats['name'] }}, 
                                        @endforeach
                                    </div>
                                </div>
                                {{-- <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0"> Suggest this Project to agencies</h6>
                                    </div>
                                    <div class="col-sm-6 text-secondary ">
                                        <form action="{{ route('AdminTool.sendEmailAgencies.send', $project->id) }}" method="POST">
                                            @csrf
                                            <select data-placeholder="Choose Agencis" multiple class="chosen-select" name='teamsIds[]'>
                                                @foreach ($allTeams as $team)
                                                <option value="{{ $team->id }}"> {{ $team->name }}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                    <div class="col-sm-3 text-secondary ">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        </form>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                @if ($status == 1)
                                    <h3>Agency who took project</h3>
                                @else
                                    <h3>Initial Propsals</h3>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            @if ($status == 1)
                                <div class="col-lg-4">
                                    <div class="card mb-3 card-projects">
                                        <a href="{{ route('AdminTool.agencies.show', $teams->id) }}"
                                            class="project-card-href project-card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <p> Agency name:</p>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <h6 class="mb-0">{{ $teams->name }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @else
                                @foreach ($propsals as $propsal)
                                    <div class="col-lg-4">
                                        <div class="card mb-3 card-projects">
                                            <a href="{{ route('AdminTool.initialProposals.show', $propsal->id) }}"
                                                class=" project-card project-card-href">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <p> Agency name:</p>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <h6 class="mt-1">{{ $propsal->team_name }}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
