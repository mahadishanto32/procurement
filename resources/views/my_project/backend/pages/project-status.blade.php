@extends('my_project.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
    <style>
        body {
            background-color: #f9f9fa
        }

        .flex {
            -webkit-box-flex: 1;
            -ms-flex: 1 1 auto;
            flex: 1 1 auto
        }

        @media (max-width:991.98px) {
            .padding {
                padding: 1.5rem
            }
        }

        @media (max-width:767.98px) {
            .padding {
                padding: 1rem
            }
        }

        .padding {
            padding: 5rem
        }

        .project-card {
            background: #fff;
            border-width: 0;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
            margin-bottom: 1.5rem
        }

        .project-card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            width: 100%;
            height: 500px;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid rgba(19, 24, 44, .125);
            border-radius: .25rem
        }

        .project-card-header {
            padding: .75rem 1.25rem;
            margin-bottom: 0;
            background-color: rgba(19, 24, 44, .03);
            border-bottom: 1px solid rgba(19, 24, 44, .125)
        }

        .project-card-header:first-child {
            border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0
        }

        .project-card-footer,
        .project-card-header {
            background-color: transparent;
            border-color: rgba(160, 175, 185, .15);
            background-clip: padding-box
        }
        #contextMenu .item {
            cursor: pointer;
            transition: 1s;
        }
        #contextMenu .item:hover{
            background: #fff5f4;
            transition: 1s;
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
                    <li class="active">{{__($title)}} List</li>

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
                            <div class="row">
                                <div class="col-md-6 d-flex justify-content-center">
                                    <div class="project-card">
                                        <div class="project-card-header">Project Status</div>
                                        <div class="project-card-body">
                                            <div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                                <div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                                    <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                                                </div>
                                                <div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                                    <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                                                </div>
                                            </div>
                                            <canvas id="chart-line" width="299" height="200" class="chartjs-render-monitor" style="display: block; width: 299px; height: 200px;"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="project-card">
                                        <div class="project-card-header">
                                            <span class="float-left">Project Report</span>
                                            <span class="float-right">
                                                @can('project-manage')
                                                    <a class="projectActionBtn" data-browse="{{ route('my_project.my-project.edit',$project->id) }}" data-action="{{ route('my_project.my-project.destroy',$project->id) }}" data-role="{{ route('my_project.project-report',$project->id) }} "data-chart="{{ route('my_project.gantt-chart',$project->id) }}" href="javascript:void(0)"><i class="las la-ellipsis-v" style="font-size: 32px;"></i></a>
                                                @endcan
                                            </span>
                                        </div>
                                        <div class="project-card-body">
                                            <ul class="d-none">
                                                @foreach($deliverables as $deliverable)
                                                    <li>{!! '<span class="projectDeliverable" data-src="'. $deliverable->weightage .'" data-action="'.($deliverable->status_at>0?(($deliverable->status_at * 100)/$deliverable->weightage):0).'">'. $deliverable->name .'</span>'.' (<span>'. $deliverable->status_at .'%</span>)' !!}</li>
                                                @endforeach
                                            </ul>
                                            <div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                                <div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                                    <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                                                </div>
                                                <div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                                    <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                                                </div>
                                            </div>
                                            <canvas id="chart-bar" width="299" height="200" class="chartjs-render-monitor" style="display: block; width: 299px; height: 200px;"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 text-right">
                                    <a href="{{ route('my_project.my-project.show',$project->id) }}">
                                        <button type="button" class="btn btn-primary">Next</button>
                                    </a>
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
    <script src='{{ asset('assets/js/Chart.bundle.min.js') }}'></script>
    <script>
        $(document).ready(function() {
            let projectDeliverables = document.querySelectorAll('.projectDeliverable');
            let projectLabels = [];
            let projectLabelsForBar = [];
            let projectData = [];
            let projectSuccess = [];
            let projectColor = [];
            let projectBorderColor = [];

            Array.from(projectDeliverables).map((item, key) => {
                let colorX = random_bg_color();
                projectLabels.push(item.innerText)
                projectLabelsForBar.push(key+1)
                projectData.push(item.getAttribute('data-src'))
                projectSuccess.push(item.getAttribute('data-action'));
                projectBorderColor.push(colorX.opacity)
                projectColor.push(colorX.color);

            })

            //random color
            function random_bg_color() {
                var x = Math.floor(Math.random() * 256);
                var y = 100+ Math.floor(Math.random() * 256);
                var z = 50+ Math.floor(Math.random() * 256);
                var bgColor = "rgb(" + x + "," + y + "," + z + ")";
                var opColor = "rgb(" + x + "," + y + "," + z +","+ 0.5+")";
                return {color:bgColor,opacity:opColor};
            }

            // pie chart
            var pieCtx = $("#chart-line");
            new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: projectLabels,
                    datasets: [{
                        data: projectData,
                        backgroundColor: projectColor
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    title: {
                        display: true,
                        text: 'Status'
                    }
                }
            });

            // bar chart
            let barCtx = document.getElementById('chart-bar').getContext('2d');
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: projectLabelsForBar,
                    datasets: [{
                        label: 'Done Tasks Percentage (%)',
                        data: projectSuccess,
                        backgroundColor: projectBorderColor,
                        borderColor: projectColor,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    title: {
                        display: true,
                        text: 'Report'
                    }
                }
            });

            // project action button
            document.querySelector('.projectActionBtn').onclick = function (e) {
                e.preventDefault();
                let mousex = e.clientX; // Gets Mouse X
                let mousey = e.clientY; // Gets Mouse Y

                $('#contextMenu').remove();
                let body = document.querySelector('body');
                body.style = 'position: relative;';

                let menu = document.createElement('div')
                menu.id = 'contextMenu';
                menu.style = `position: absolute; left: ${(mousex-160)}px; top: ${mousey}px; z-index: 1000000; width: 150px; background: #E5E5E5; border-radious: 5px;`

                let menuItemEdit = document.createElement('div')
                menuItemEdit.className = 'item cMEdit p-2'
                menuItemEdit.innerHTML = '<i class="las la-edit" style="font-size: 16px;"></i>Edit'

                let menuItemDelete = document.createElement('div')
                menuItemDelete.className = 'item cMDelete p-2'
                menuItemDelete.innerHTML = '<i class="las la-trash" style="font-size: 16px;"></i>Delete'

                let menuItemReport = document.createElement('div')
                menuItemReport.className = 'item cMReport p-2'
                menuItemReport.innerHTML = '<i class="las la-file-alt" style="font-size: 16px;"></i>Report'

                let menuItemGanttChart = document.createElement('div')
                menuItemGanttChart.className = 'item cMGanttChart p-2'
                menuItemGanttChart.innerHTML = '<i class="las la-chart-line" style="font-size: 16px;"></i>Gantt Chart'

                menu.appendChild(menuItemGanttChart)
                menu.appendChild(menuItemReport)
                menu.appendChild(menuItemEdit)
                menu.appendChild(menuItemDelete)
                body.appendChild(menu);

                let ganttChartBtn = document.querySelector('.cMGanttChart');
                ganttChartBtn.onclick = function (){

                    ganttChartForProject(e.target.parentElement.getAttribute('data-chart'));
                };

                let reportBtn = document.querySelector('.cMReport');
                reportBtn.onclick = function (){
                    reportForProject(e.target.parentElement.getAttribute('data-role'));
                };

                let editBtn = document.querySelector('.cMEdit');
                editBtn.onclick = function (){
                    editAndUpdateForProject(e.target.parentElement.getAttribute('data-browse'))
                };

                let deleteBtn = document.querySelector('.cMDelete');
                deleteBtn.onclick = function (){
                    deleteActionForProject(e.target.parentElement.getAttribute('data-action'))
                };
            }

            const editAndUpdateForProject = (url) => {
                $('#contextMenu').remove();
                window.location = url
            }

            const ganttChartForProject = (url) => {
                $('#contextMenu').remove();
                window.location = url
            }

            const deleteActionForProject = (url) => {
                $.ajax({
                    type: 'delete',
                    url: url,
                    success:function (data){
                        if (data.status === 200){
                            notify(data.message, 'success')
                            window.location.href = data.routeUrl
                        }else {
                            notify(data, 'error')
                        }
                    }
                })
            }

            const reportForProject = (url) => {
                $('#contextMenu').remove();
                window.location = url
            }

            window.onmousedown = function (e) {
                if(e.target.classList.contains('cMReport')){
                    e.preventDefault();
                }else if (e.target.classList.contains('cMEdit')){
                    e.preventDefault();
                }else if (e.target.classList.contains('cMDelete')){
                    e.preventDefault();
                }else if (e.target.classList.contains('cMGanttChart')){
                    e.preventDefault();
                }else {
                    let mouseEvent = window.event;
                    if (mouseEvent.which === 1) {
                        $('#contextMenu').remove();
                    }
                }

            }
        });
    </script>
@endsection
