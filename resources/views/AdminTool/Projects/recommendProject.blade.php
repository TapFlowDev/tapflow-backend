@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <h1>Recommend Project</h1>
                    <div class="row">
                        <div class="col-6">
                            <form action="{{ route('AdminTool.recommendProject.show', $project->id) }}" method="GET">
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
                            </form>
                            <hr>
                            <form action="{{ route('AdminTool.sendEmailAgencies.send', $project->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <h5>Project Categories:</h5>
                                    @if(count($agencies)>0)
                                    <select data-placeholder="Choose Agencis" multiple class="chosen-select" name='teamsIds[]'>
                                        @foreach ($agencies as $team )
                                        <option value="{{ $team->id }}"> {{ $team->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-success mt-3">Send Emails</button>
                                    @else
                                    No Data
                                    @endif
                                </div>

                            </form>
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
@endsection
