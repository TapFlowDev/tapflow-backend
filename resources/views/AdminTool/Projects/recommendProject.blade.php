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
                                                <option value="{{ $team->id }}"> {{ $team->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-success mt-3">Send Emails</button>
                                        <button type="button" class="btn btn-warning mt-3 chosen-toggle select">Select all</button>
                                        <button type="button" class="btn btn-danger mt-3 chosen-toggle deselect">Deselect all</button>
                                    @else
                                        No Data
                                    @endif
                                </div>
                            </form>
                            <hr>
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
