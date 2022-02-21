@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
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
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0"> Suggest this Project to agencies</h6>
                                    </div>
                                    <div class="col-sm-6 text-secondary ">
                                        {{-- {{ $project->requirements_description }} --}}

                                        {{-- <select name="languageSelect[]" multiple id="languageSelect">
                                            <option value="">Select An Option</option>
                                            <option value="1">Option 1</option>
                                            <option value="2">Option 2</option>
                                            <option value="3">Option 3</option>
                                        </select> --}}
                                        <form action="{{ route('AdminTool.sendEmailAgencies.send', $project->id) }}" method="POST">
                                            @csrf
                                            <select data-placeholder="Choose Agencis" multiple class="chosen-select" name='teamsIds[]'>
                                                @foreach ($allTeams as $team )
                                                <option value="{{ $team->id }}"> {{ $team->name }}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                    <div class="col-sm-3 text-secondary ">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            @if ($status == 1)
                                <h3>Agency who took project</h3>
                            @else
                                <h3>Agencis who's intrested in this project</h3>
                            @endif
                        </div>
                        <div class="row">
                            @if ($status == 1)
                                <div class="col-lg-4">
                                    <div class="card mb-3 card-projects">
                                        <div class="card-body">
                                            <div class="row">
                                                <a href="{{ route('AdminTool.agencies.show', $teams->id) }}"
                                                    class="project-card-href">
                                                    <div class="col-sm-12 project-card">
                                                        <p> Agency name:</p>
                                                        <h6 class="mb-0">{{ $teams->name }}</h6>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                @foreach ($teams as $team)
                                    <div class="col-lg-4">
                                        <div class="card mb-3 card-projects">
                                            <div class="card-body">
                                                <div class="row">
                                                    <a href="{{ route('AdminTool.agencies.show', $team->id) }}"
                                                        class="project-card-href">
                                                        <div class="col-sm-12 project-card">
                                                            <p> Agency name:</p>
                                                            <h6 class="mb-0">{{ $team->name }}</h6>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
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
    <script type="text/javascript">
        $(function() {
            $(".chosen-select").chosen();
        });
    </script>
    <script>
        jQuery('#languageSelect').multiselect({
            columns: 1,
            placeholder: 'Select Languages',
            search: true,
            selectAll: true

        });
    </script>
@endsection
