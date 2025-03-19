@extends('my_project.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
@endsection
@section('main-content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{  route('pms.dashboard') }}">{{ __('Home') }}</a>
                    </li>
                    <li>
                        <a href="#">PMS</a>
                    </li>
                    <li class="active">{{__($title)}} List</li>
                    <li class="top-nav-btn">
                        <a href="{{ route('my_project.grid') }}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Project Grid View"> <i class="las la-border-all">{{ __('Grid View') }}</i></a>
                        @can('project-manage')
                        <a href="{{ url('/pms/my-project/create') }}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add New Project"> <i class="las la-plus">Add</i></a>
                        @endcan
                    </li>
                </ul><!-- /.breadcrumb -->

            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body">
                            <table  id="dataTable" class="table table-striped table-bordered table-head" border="1">
                                <thead>
                                <tr>
                                    <th width="5%">{{__('Indent No')}}</th>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Key Stakeholder')}}</th>
                                    <th>{{__('Signatured by')}}</th>
                                    <th class="text-center">{{__('Option')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($projects as $project)
                                    <tr>
                                        <td>{{ $project->indent_no }}</td>
                                        <td>{{ $project->name }}</td>
                                        <td>
                                            @foreach($project->departments as $department)
                                                <p class="badge badge-primary">{{ $department->hr_department_name }}</p>
                                            @endforeach
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <select class="permission form-control" style="width: 100%" data-action="{{ route('my_project.insert-project-action', $project) }}">
                                                    <option {{ $project->status === 'pending'?'selected':'' }} value="pending">Pending</option>
                                                    <option {{ $project->status === 'approved'?'selected':'' }} value="approved">Approved</option>
                                                    <option {{ $project->status === 'halt'?'selected':'' }} value="halt">Halt</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a class="m-1" href="{{ route('my_project.my-project.show',$project->id) }}">
                                                <button class="btn btn-info btn-sm">{{ __('View') }}</button>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $projects->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-script')
    <script>
        (function ($){
            "use script";
            let signatureBtn = document.querySelectorAll('.permission');
            Array.from(signatureBtn).map(item => {
                item.onchange = function (e) {
                    e.preventDefault();
                    $.ajax({
                        type: 'post',
                        url: e.target.getAttribute('data-action'),
                        data: {
                            action: e.target.value
                        },
                        success:function (data){
                            if(data.status === 200){
                                notify(data.message, 'success');
                            }
                        }
                    })
                }
            });
        })(jQuery);
    </script>
@endsection
