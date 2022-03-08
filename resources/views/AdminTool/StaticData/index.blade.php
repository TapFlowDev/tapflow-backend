@extends('templates.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container">
                    <h1> Platform Content </h1>
                    <a class="btn btn-sm btn-success float-right mb-3" href="{{ route('AdminTool.staticData.create') }}"
                        role="button">Add</a>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#id</th>
                                    <th scope="col">Image</th>
                                    <th scope="col">Link</th>
                                    <th scope="col">Text</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $info)
                                    <tr>
                                        <th scope="row">{{$info->id }}</th>
                                        <td>{{$info->image }}</td>
                                        <td>{{$info->link }}</td>
                                        <td>{{$info->text }}</td>
                                        <td>
                                            <a class="btn btn-sm btn-primary"
                                                href="{{ route('AdminTool.staticData.edit',$info->id) }}" role="button">edit</a>
                                        </td> 
                                        <td>
                                        @if ($info->hidden==0)
                                            <a class="btn btn-sm btn-danger"
                                                href="{{ route('AdminTool.hideContent.hideContent',$info->id) }}" role="button">hide</a>
                                                @endif
                                                @if ($info->hidden==1)
                                            <a class="btn btn-sm btn-success"
                                                href="{{ route('AdminTool.showContent.showContent',$info->id) }}" role="button">show</a>
                                                @endif
                                        </td>
                                    </tr>
                                @endforeach
            
                            </tbody>
                        </table>
                      
                </div> 
            </div>
        </div>
    </div>

@endsection
