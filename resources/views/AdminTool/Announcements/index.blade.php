@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <h1> Announcements </h1>
                    <a class="btn btn-sm btn-success float-right mb-3" href="{{ route('AdminTool.announcements.create') }}"
                        role="button">Add</a>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#id</th>
                                <th scope="col">Content</th>
                                <th scope="col">Template</th>
                                <th scope="col">Date Created</th>
                                <th scope="col">Date Modified</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($announcements as $announcement)
                                <tr>
                                    <th scope="row">{{ $announcement->id }}</th>
                                    {{-- <td>{{ str_limit(strip_tags($announcement->content), 25) }}</td> --}}
                                    <td>{{ str_limit(strip_tags($announcement->stripedContent), 25) }}</td>
                                    <td>{{ $announcement->template }}</td>
                                    <td>{{ $announcement->created_at }}</td>
                                    <td>{{ $announcement->updated_at }}</td>
                                    <td>
                                        <a class="btn btn-sm btn-primary"
                                            href="{{ route('AdminTool.announcements.edit', $announcement->id) }}"
                                            role="button">edit</a>
                                        <button class="btn btn-sm btn-danger"
                                            onclick="event.preventDefault();
                                                        document.getElementById('delete-user-form-{{ $announcement->id }}').submit()">Delete</button>
                                        <form id="delete-user-form-{{ $announcement->id }}"
                                            action="{{ route('AdminTool.announcements.destroy', $announcement->id) }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                            @method("DELETE")
                                        </form>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    {{ $announcements->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection
