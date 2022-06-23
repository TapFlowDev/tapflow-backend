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
                                        <h6 class="mb-0">Project Name:</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <a class="team-name-link"
                                            href="{{ route('AdminTool.projects.show', $proposal->project_id) }}"
                                            target="_blanck" role="button">{{ $proposal->projectName }}
                                        </a>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Created</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        {{ date('Y-m-d', strtotime($proposal->created_at)) }}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Agency Name:</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <a class="team-name-link"
                                            href="{{ route('AdminTool.agencies.show', $proposal->team_id) }}"
                                            target="_blanck" role="button">{{ $proposal->teamName }}
                                        </a>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Agency Admin Name:</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <a class="team-name-link"
                                            href="{{ route('AdminTool.freelancers.show', $proposal->user_id) }}"
                                            target="_blanck" role="button">{{ $proposal->teamAdminName }}
                                        </a>
                                        
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Agency Admin Email:</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">

                                        <a class="team-name-link" href="mailto:{{ $proposal->teamAdminEmail }}"
                                            target="_blanck" role="button">{{ $proposal->teamAdminEmail }}
                                        </a>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-12 mb-2">
                                        <h6 class="mb-0">Requirement</h6>
                                    </div>
                                    <div class="col-sm-12 text-secondary">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Skills</th>
                                                    <th scope="col">Rate</th>
                                                    <th scope="col">Quantity</th>
                                                    <th scope="col">Seniority</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($proposal->requirementDetails['reqArr'] as $req)
                                                    <tr>
                                                        <th>
                                                            @foreach ( $req['skills'] as $skill )
                                                                ${{ $skill }}
                                                                @if (!$loop->last)
                                                                    ,
                                                                @endif
                                                            @endforeach
                                                        </th>
                                                        <td>{{ $req['hourlyRate'] }}</td>
                                                        <td>{{ $req['quantity'] }}</td>
                                                        <td>
                                                            {{ $req['seniority'] }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                    
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Message</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        {{ $proposal->our_offer }}
                                    </div>
                                </div>
                                {{-- <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Status</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        @if ($proposal->status == 0)
                                            Pending
                                        @elseif ($proposal->status == 1)
                                            Approved
                                        @endif
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
