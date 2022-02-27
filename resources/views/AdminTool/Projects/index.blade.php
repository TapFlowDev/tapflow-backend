@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <h1> Projects </h1>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#id</th>
                                <th scope="col">Name</th>
                                <th scope="col">Company Name</th>
                                {{-- <th scope="col">Status</th> --}}
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projects as $project)
                                <tr>
                                    <th scope="row">{{ $project->id }}</th>
                                    <td>{{ $project->name }}</td>
                                    <td class="team-name-link">
                                        <a class=""
                                            href="{{ route('AdminTool.companies.show', $project->company_id) }}"
                                            role="button">{{ $project->company_name }}
                                        </a>
                                    </td>
                                    {{-- <td>
                                        @if ($project->status == 0)
                                            Pending
                                        @elseif ($project->status == 1)
                                            Active
                                        @elseif ($project->status == 2)
                                            Finished
                                        @endif
                                    </td> --}}
                                    <td>
                                        <a class="btn btn-sm btn-primary"
                                            href="{{ route('AdminTool.projects.show', $project->id) }}"
                                            role="button">View</a>
                                        <a class="btn btn-sm btn-info"
                                            href="{{ route('AdminTool.recommendProject.show', $project->id) }}"
                                            role="button">Recommend Project</a>
                                        {{-- <button class="btn btn-sm btn-danger" 
                                onclick="event.preventDefault();
                                document.getElementById('delete-project-form-{{ $project->id }}').submit()">Delete</button>
                            <form id="delete-project-form-{{ $project->id }}"  action="{{ route('AdminTool.projects.destroy', $project->id) }}" method="POST"
                                style="display: none;">
                            @csrf
                            @method("DELETE")
                            </form> --}}
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
