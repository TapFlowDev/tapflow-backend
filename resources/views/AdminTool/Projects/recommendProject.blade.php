@extends('templates.main')
@section('content')
    <div class="container">
        <div class="content-container">
            <div class="row">
                <div class="col-12">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @elseif (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    <h1>Recommend Project</h1>
                    <div class="row">
                        <div class="col-6">
                            <form action="{{ route('AdminTool.sendEmailAgencies.send', $project->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <h5>Project Categories:</h5>
                                    @if (count($agencies) > 0)
                                        <select data-placeholder="Choose Agencis" multiple class="chosen-select teams"
                                            name='teamsIds[]'>
                                            @foreach ($agencies as $team)
                                                <option value="{{ $team->id }}"
                                                    @if (in_array($team->id, $matchedAgenciesIds)) selected @endif> {{ $team->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-success mt-3">Send Emails</button>
                                        <button type="button" class="btn btn-warning mt-3 chosen-toggle select">Select
                                            all</button>
                                        <button type="button" class="btn btn-danger mt-3 chosen-toggle deselect">Deselect
                                            all</button>
                                    @else
                                        No Data
                                    @endif
                                </div>
                            </form>
                            <hr>
                            {{-- <form action="{{ route('AdminTool.recommendProject.show', $project->id) }}" method="GET">
                                @csrf
                                @method('GET')
                                <div class="mb-3">
                                    <h5>Project Categories:</h5>
                                    @foreach ($categories as $category)
                                        <label for="name" class="form-label">Category Name :
                                            {{ $category->name }}</label>
                                        <select data-placeholder="Choose Agencis" multiple class="chosen-select"
                                            name='subs[]'>
                                            @foreach ($category['subs'] as $sub)
                                            <option value="{{ $sub->id }}"
                                                @if (in_array($sub->id, $projectCategories)) selected @endif> {{ $sub->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <br>
                                        <br>
                                    @endforeach
                                </div>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </form> --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h2> Matched Agencies </h2>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#id</th>
                                <th scope="col">Name</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($matchedAgencies as $agency)
                                <tr>
                                    <th scope="row">{{ $agency->id }}</th>
                                    <td>{{ $agency->name }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-success"
                                            onclick="event.preventDefault();
                                            document.getElementById('verifyProject-form-{{ $agency->id }}').submit()">Send
                                            Email</button>
                                        <form id="verifyProject-form-{{ $agency->id }}"
                                            action="{{ route('AdminTool.sendEmailAgency.send', $project->id) }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                            @method('POST')
                                            <input type="hidden" name="teamId" value="{{ $agency->id }}">

                                        </form>
                                        <button class="btn btn-sm btn-danger"
                                            onclick="event.preventDefault();
                                            document.getElementById('delete-project-form-{{ $project->id }}').submit()">Unmatch</button>
                                        <form id="delete-project-form-{{ $project->id }}"
                                            action="{{ route('AdminTool.removeMatch.destroy', $project->id) }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                            @method('POST')
                                            <input type="hidden" name="teamId" value="{{ $agency->id }}">
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function() {
            $(".chosen-select").chosen();
        });
        $('.chosen-toggle').each(function(index) {
            console.log(index);
            $(this).on('click', function() {
                console.log($(this).parent().find('option').text());
                $(this).parent().find('option').prop('selected', $(this).hasClass('select')).parent()
                    .trigger('chosen:updated');
            });
        });
    </script>
@endsection
