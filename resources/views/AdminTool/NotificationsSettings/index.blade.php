@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <h1> Notifications </h1>
                    <a class="btn btn-sm btn-success float-right mb-3" href="{{ route('AdminTool.notificationSettings.create') }}"
                        role="button">Add</a>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#id</th>
                                    <th scope="col">template</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($notifications as $notification)
                                    <tr>
                                        <th scope="row">{{ $notification->id }}</th>
                                        <td>{{ $notification->email_template }}</td>
                                        <td>
                                            @if ($notification->field_type==1)
                                                Text Field
                                                @elseif ($notification->field_type==2)
                                                Text Area
                                            @endif
                                        </td>
                                        <td>
                                            {{-- <a class="btn btn-sm btn-primary"
                                                href="{{ route('AdminTool.formOptions.edit', $notification->id) }}" role="button">edit</a>
                                             --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $notifications->links() }}
                </div> 
            </div>
        </div>
    </div>

@endsection
