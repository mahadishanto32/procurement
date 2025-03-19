
<?php $__env->startSection('title', config('app.name', 'laravel'). ' | '.$title); ?>
<?php $__env->startSection('page-css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('plugins/plugin/percircle/percircle.css')); ?>">
    <style>
        .container{
            height: 455px!important;
        }
        .projectGrid{
            overflow-y: scroll!important;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main-content'); ?>
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="<?php echo e(route('pms.dashboard')); ?>"><?php echo e(__('Home')); ?></a>
                    </li>
                    <li>
                        <a href="#">PMS</a>
                    </li>
                    <li class="active"><?php echo e(__($title)); ?> List</li>

                    <li class="top-nav-btn">
                        <a href="<?php echo e(route('my_project.my-project.index')); ?>" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Project List View"> <i class="las la-list-ul"><?php echo e(__('List View')); ?></i></a>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('project-manage')): ?>
                        <a href="<?php echo e(url('/pms/my-project/create')); ?>" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add New Project"> <i class="las la-plus">Add</i></a>
                        <?php endif; ?>
                    </li>
                </ul><!-- /.breadcrumb -->

            </div>

            <div class="page-content">
                <div class="container-fluid">
                    <div class="row" id="reportVariance" data-action="<?php echo e(route('my_project.status-wise-project-chart')); ?>">
                        <div class="col-md-6 mt-3">
                            <div class="card">
                                <div class="card-header">
                                    <p class="text-center text-uppercase h4">all projects</p>
                                </div>
                                <div class="card-body container projectGrid">
                                    <div class="row">
                                        <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a class="col-lg-6 col-md-12 col-sm-12" href="<?php echo e(route('my_project.status', $project->id)); ?>">
                                                <div class="card card-info  text-center p-0 rounded">
                                                    <div class="card-header">
                                                        <p class="h5 text-dark allProjectNames" data-percent="<?php echo e($project->status_at); ?>"><?php echo e($project->name); ?></p>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row text-left border border-top-0 border-left-0 border-right-0 pb-3">
                                                            <div class="col-12 text-center"><?php echo date('d-M-y', strtotime($project->start_date)) . '<span class="font-weight-bold h5"> to </span>'.date('d-M-y', strtotime($project->end_date)); ?></div>
                                                        </div>
                                                        <div class="row pt-3">
                                                            <div id="<?php echo e($project->indent_no); ?>" data-percent="<?php echo e($project->status_at); ?>" class="projectProgress mx-auto small">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="card my-2">
                                <div class="card-header">
                                    <p class="text-center text-uppercase h4">Continuous Projects</p>
                                </div>
                                <div class="card-body container">
                                    <div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                        <div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                            <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                                        </div>
                                        <div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                            <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                                        </div>
                                    </div>
                                    <canvas id="chart-bar1" width="100%" height="75%" class="chartjs-render-monitor" style="display: block; width: 299px; height: 200px;"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="card my-2">
                                <div class="card-header">
                                    <p class="text-center text-uppercase h4">Completed Project</p>
                                </div>
                                <div class="card-body container">
                                    <div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                        <div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                            <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                                        </div>
                                        <div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                            <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                                        </div>
                                    </div>
                                    <canvas id="chart-bar2" width="100%" height="75%" class="chartjs-render-monitor" style="display: block; width: 299px; height: 200px;"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="card my-2">
                                <div class="card-header">
                                    <p class="text-center text-uppercase h4">exceed deadline</p>
                                </div>
                                <div class="card-body container">
                                    <div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                        <div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                            <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                                        </div>
                                        <div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                            <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                                        </div>
                                    </div>
                                    <canvas id="chart-bar3" width="100%" height="75%" class="chartjs-render-monitor" style="display: block; width: 299px; height: 200px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo e($projects->links()); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-script'); ?>
    <script src="<?php echo e(asset('plugins/plugin/percircle/percircle.js')); ?>"></script>
    <script src='<?php echo e(asset('assets/js/Chart.bundle.min.js')); ?>'></script>
    <script>
        (function ($){
            "use script";
            let projectLabel = [];
            let projectStatus = [];
            let projectBackgroundColor = [];
            let projectBorderColor = [];

            const random_bg_color = () => {
                var x = Math.floor(Math.random() * 256);
                var y = 100+ Math.floor(Math.random() * 256);
                var z = 50+ Math.floor(Math.random() * 256);
                var bgColor = "rgb(" + x + "," + y + "," + z + ")";
                var opColor = "rgb(" + x + "," + y + "," + z +","+ 0.8+")";
                return {color:bgColor,opacity:opColor};
            }

            let projects = document.querySelectorAll('.projectProgress');
            Array.from(projects).map(item => {
                let itemColor = random_bg_color()
                item.parentElement.parentElement.parentElement.firstChild.setAttribute('style', `background: ${itemColor.color}`)
                item.parentElement.parentElement.parentElement.setAttribute('style', `border: 1px solid ${itemColor.color}`)
                $(`#${item.id}`).percircle({
                    progressBarColor: itemColor.color,
                });

                projectLabel.push(item.parentElement.parentElement.parentElement.querySelector('.allProjectNames').innerText);
                projectStatus.push(item.parentElement.parentElement.parentElement.querySelector('.allProjectNames').getAttribute('data-percent'));
                projectBackgroundColor.push(itemColor.opColor);
                projectBorderColor.push(itemColor.color);
            });

            let reportVariance = document.querySelector('#reportVariance');
            $.ajax({
                type: 'get',
                url: reportVariance.getAttribute('data-action'),
                success:(data)=> chartVariance(data.continuousProjects, data.completedProjects, data.exceedProjects)
            })

            const chartVariance = (continuedProjects, completedProjects, exceedProjects) => {
                continuedProjectsChart(continuedProjects)
                completedProjectsChart(completedProjects)
                exceedProjectsChart(exceedProjects)
            }

            const continuedProjectsChart = (continuedProjects) => {
                let continueProjectLabel = [];
                let continueProjectStatus = [];
                let continueProjectBackgroundColor = [];
                let continueProjectBorderColor = [];

                Array.from(continuedProjects).map((item, index) => {
                    let itemColor = random_bg_color()
                    continueProjectLabel.push(item.name);
                    continueProjectStatus.push(item.status_at);
                    continueProjectBackgroundColor.push(itemColor.color);
                    continueProjectBorderColor.push(itemColor.color);
                });
                // bar chart
                let barCtx = document.getElementById('chart-bar1').getContext('2d');
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: continueProjectLabel,
                        datasets: [{
                            label: 'Continuing Projects Percentage (%)',
                            data: continueProjectStatus,
                            backgroundColor: continueProjectBackgroundColor,
                            borderColor: continueProjectBorderColor,
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
            }

            const completedProjectsChart = (completedProjects) => {
                let completedProjectLabel = [];
                let completedProjectStatus = [];
                let completedProjectBackgroundColor = [];
                let completedProjectBorderColor = [];

                Array.from(completedProjects).map(item => {
                    let itemColor = random_bg_color()
                    completedProjectLabel.push(item.name);
                    completedProjectStatus.push(item.status_at);
                    completedProjectBackgroundColor.push(itemColor.color);
                    completedProjectBorderColor.push(itemColor.color);
                });
                // bar chart
                let barCtx = document.getElementById('chart-bar2').getContext('2d');
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: completedProjectLabel,
                        datasets: [{
                            label: 'Completed Projects Percentage (%)',
                            data: completedProjectStatus,
                            backgroundColor: completedProjectBackgroundColor,
                            borderColor: completedProjectBorderColor,
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
            }

            const exceedProjectsChart = (exceedProjects) => {
                let exceedProjectLabel = [];
                let exceedProjectStatus = [];
                let exceedProjectBackgroundColor = [];
                let exceedProjectBorderColor = [];

                Array.from(exceedProjects).map(item => {
                    let itemColor = random_bg_color()
                    exceedProjectLabel.push(item.name);
                    exceedProjectStatus.push(item.status_at);
                    exceedProjectBackgroundColor.push(itemColor.color);
                    exceedProjectBorderColor.push(itemColor.color);
                });
                // bar chart
                let barCtx = document.getElementById('chart-bar3').getContext('2d');
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: exceedProjectLabel,
                        datasets: [{
                            label: 'Exceed Projects Percentage (%)',
                            data: exceedProjectStatus,
                            backgroundColor: exceedProjectBackgroundColor,
                            borderColor: exceedProjectBorderColor,
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
            }



        })(jQuery);
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('my_project.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/my_project/backend/pages/index-grid.blade.php ENDPATH**/ ?>