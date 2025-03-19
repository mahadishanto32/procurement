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
                    <li class="active">{{__($title)}}</li>

                    <li class="top-nav-btn">
                        <a href="{{ route('my_project.grid') }}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Project Grid View"> <i class="las la-border-all">{{ __('Grid View') }}</i></a>

                        <a href="{{ route('my_project.my-project.index') }}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Project List View"> <i class="las la-list-ul">{{ __('List View') }}</i></a>
                    </li>
                </ul><!-- /.breadcrumb -->

            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body">
                            <div class="card">
                                <div class="card-head text-center font-weight-bold text-uppercase h4">
                                    Overall Progress Summary
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered" width="100%">
                                        <thead>
                                        <tr>
                                            <th width="5%">SL</th>
                                            <th width="35%">Deliverables</th>
                                            <th width="15%">Deliverables Weightage (%)</th>
                                            <th width="15%" class="text-center">Total (%)</th>
                                            <th width="15%" class="text-center">Present Progress (%)</th>
                                            <th width="15%">{{ $lastWeekProgress?$lastWeekProgress->week_no:1 }} Week\'s Progress (%)</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td colspan="3" width="55%" class="p-0">
                                                <table width="100%" class="table table-striped m-0">
                                                    <tbody>
                                                    @foreach($project->deliverables as $key => $deliverable)
                                                    <tr>
                                                        <td width="5%">{{ $key+1 }}</td>
                                                        <td width="35%">{{ $deliverable->name }}</td>
                                                        <td width="15%">{{ $deliverable->weightage }}</td>
                                                    </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </td>
                                            <td width="15%" class="text-center">{{ $project->deliverables->sum('weightage') }}</td>
                                            <td width="15%" class="text-center">{{ $project->deliverables->sum('status_at') }}</td>
                                            <td width="15%" class="text-center">{{ $lastWeekProgress?$lastWeekProgress->status_at:0 }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div> <!-- ok -->

                            <div class="card">
                                <div class="card-head text-center font-weight-bold text-uppercase h4">
                                    Sanctioned Budget VS Incurred Cost
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped" width="100%">
                                        <thead>
                                        <tr>
                                            <th width="5%">SL</th>
                                            <th>Deliverables Name</th>
                                            <th>Tentative Cost</th>
                                            <th>Section Amount (DEG)</th>
                                            <th>Cost Incurred (As per Bill)</th>
                                            <th>Consumed Fund (%)</th>
                                            <th>Remaining Fund (%)</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($project->deliverables as $key => $deliverable)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $deliverable->name }}</td>
                                            <td>{{ $deliverable->budget }}</td>
                                            @if($key===0)
                                                <td class="text-center" rowspan="{{ $project->deliverables->count() }}">{{ $project->budget }}</td>
                                            @endif
                                            <td>{{ deliverableWiseBudget($project->id, $deliverable->id) }}</td>
                                            @if($key===0)
                                                <td class="text-center" rowspan="{{ $project->deliverables->count() }}">{!! (consumedBudget($project->id) / $project->budget) * 100 !!}</td>
                                                <td class="text-center" rowspan="{{ $project->deliverables->count() }}">

                                                    {!! 100 - (floatval(consumedBudget($project->id) / floatval($project->budget)) * 100) !!}</td>
                                            @endif
                                        </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div> <!-- ok -->

                            <div class="card">
                                <div class="card-head text-center font-weight-bold text-uppercase h4">
                                    Deliverables Progress Summary
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped" width="100%">
                                        <thead>
                                        <tr>
                                            <th width="5%">SL</th>
                                            <th>Deliverables Name</th>
                                            <th>No. of Tasks</th>
                                            <th>Completed</th>
                                            <th>in progress</th>
                                            <th>Not Yet Started</th>
                                            <th>Progress (%)</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($project->deliverables as $key => $deliverable)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $deliverable->name }}</td>
                                            @php
                                                $tasks = 0;
                                                $completed = 0;
                                                $inprogress = 0;
                                                $notStarted = 0;
                                                foreach($deliverable->subDeliverables as $subDeliverable){
                                                    $tasks = $tasks + $subDeliverable->projectTasks->count();
                                                    $completed = $completed + $subDeliverable->projectTasks->where('status', 'done')->count();
                                                    $inprogress = $inprogress + $subDeliverable->projectTasks->where('status', 'processing')->count();$notStarted = $notStarted + $subDeliverable->projectTasks->where('status', 'pending')->count();
                                                }
                                            @endphp
                                            <td>{{ $tasks }}</td>
                                            <td>{{ $completed }}</td>
                                            <td>{{ $inprogress }}</td>
                                            <td>{{ $notStarted }}</td>
                                            <td class="text-center">{{ $deliverable->status_at }}</td>
                                        </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div> <!-- ok -->

                            <div class="card">
                                <div class="card-head text-center font-weight-bold text-uppercase h4">
                                    Days Comparison Between Plan VS Passed
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped" width="100%">
                                        <thead>
                                        <tr>
                                            <th width="5%">SL</th>
                                            <th>Deliverables Name</th>
                                            <th class="text-center">Planned Days</th>
                                            <th class="text-center">Passed Days</th>
                                            <th class="text-center">Remaining Days</th>
                                            <th class="text-center">Remaining Works</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($project->deliverables as $key => $deliverable)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $deliverable->name }}</td>
                                            <td class="text-center">{!! ((strtotime($deliverable->end_at) - strtotime($deliverable->start_at))/ (24*60*60))+1 !!}</td>
                                            <td class="text-center">{!! (round((time() - strtotime($deliverable->start_at))/ (24*60*60))+1) > 0? (round((time() - strtotime($deliverable->start_at))/ (24*60*60))+1):0 !!}</td>
                                            <td class="text-center">{!! (((strtotime($deliverable->end_at) - strtotime($deliverable->start_at))/ (24*60*60))+1)-((round((time() - strtotime($deliverable->start_at))/ (24*60*60))+1) > 0? (round((time() - strtotime($deliverable->start_at))/ (24*60*60))+1):0) !!}</td>
                                            <td class="text-center">{!! (100 - $deliverable->status_at).'%' !!}</td>
                                        </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')

@endsection
