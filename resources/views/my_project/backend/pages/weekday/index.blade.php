@extends('my_project.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
    <style>
        input:checked {

        }
    </style>
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
                    <li class="active">{{__($title)}} </li>
                    <li class="top-nav-btn">
                        <a href="{{ route('my_project.grid') }}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Project Grid View"> <i class="las la-border-all">{{ __('Grid View') }}</i></a>

                        <a href="{{ url('/pms/my-project/create') }}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add New Project"> <i class="las la-plus">Add</i></a>
                    </li>
                </ul><!-- /.breadcrumb -->

            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body">
                            <form action="{{ route('my_project.day-setup.store') }}" method="post">
                                @csrf
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th width="60%">Name</th>
                                        <th width="10%">Working days</th>
                                        <th width="10%">Report Day</th>
                                        <th width="20%">Working Hour</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($weekdays as $weekday)
                                        <tr>
                                            <td>{{ $weekday->name }}</td>
                                            <td class="text-center"><input type="checkbox" name="work_day[]"  {!! $weekday->work_on?'checked':'' !!} value="{{ $weekday->name }}"></td>
                                            <td class="text-center"><input type="radio" name="report_day" {!! $weekday->report_on?'checked':'' !!} value="{{ $weekday->name }}"></td>
                                            <td><input class="form-control bg-white" type="number" name="hour[]" value="{{ $weekday->hour }}"></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="form-group text-right">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-script')

@endsection
