@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <h1> Form Options </h1>
                    <a class="btn btn-sm btn-success float-right mb-3" href="{{ route('AdminTool.formOptions.create') }}"
                        role="button">Add</a>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#id</th>
                                    <th scope="col">Label</th>
                                    <th scope="col">Field Type</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($formOptions as $option)
                                    <tr>
                                        <th scope="row">{{ $option->id }}</th>
                                        <td>{{ $option->label }}</td>
                                        <td>
                                            @if ($option->field_type==1)
                                                Text Field
                                                @elseif ($option->field_type==2)
                                                Text Area
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-primary"
                                                href="{{ route('AdminTool.formOptions.edit', $option->id) }}" role="button">edit</a>
                                            <button class="btn btn-sm btn-danger"
                                                onclick="event.preventDefault();
                                                        document.getElementById('delete-cat-form-{{ $option->id }}').submit()">Delete</button>
                                            <form id="delete-cat-form-{{ $option->id }}"
                                                action="{{ route('AdminTool.formOptions.destroy', $option->id) }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                                @method("DELETE")
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
            
                            </tbody>
                        </table>
                        {{ $formOptions->links() }}
                </div> 
            </div>
        </div>
    </div>

@endsection
