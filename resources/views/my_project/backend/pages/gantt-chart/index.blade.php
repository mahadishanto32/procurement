@extends('my_project.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
    <style>
        #chartdiv {
            width: 100%;
            height: 500px;
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
                            <!-- HTML -->
                            <div id="chartdiv" data-action="{{ route('my_project.gantt-chart-data',$project->id) }}"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-script')
    <!-- Resources -->
    <script src="{{ asset('plugins/plugin/amcharts5/index.js') }}"></script>
    <script src="{{ asset('plugins/plugin/amcharts5/xy.js') }}"></script>
    <script src="{{ asset('plugins/plugin/amcharts5/themes/Animated.js') }}"></script>

    <!-- Chart code -->
    <script>
        am5.ready(function() {
            // console.log(new Date(2016, 0, 1).getTime());

// Create root element
// https://www.amcharts.com/docs/v5/getting-started/#Root_element
            var root = am5.Root.new("chartdiv");
            root.dateFormatter.setAll({
                dateFormat: "yyyy-MM-dd",
                dateFields: ["valueX", "openValueX"]
            });


// Set themes
// https://www.amcharts.com/docs/v5/concepts/themes/
            root.setThemes([
                am5themes_Animated.new(root)
            ]);


// Create chart
// https://www.amcharts.com/docs/v5/charts/xy-chart/
            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: false,
                panY: false,
                wheelX: "panX",
                wheelY: "zoomX",
                layout: root.verticalLayout
            }));


// Add legend
// https://www.amcharts.com/docs/v5/charts/xy-chart/legend-xy-series/
            var legend = chart.children.push(am5.Legend.new(root, {
                centerX: am5.p50,
                x: am5.p50
            }))

            var colors = chart.get("colors");

            var data = [];
            var module = []
// Data
            $.ajax({
                type: 'get',
                url: $("#chartdiv").attr("data-action"),
                success: (datas) => {
                    // console.log(datas)
                    Array.from(datas).map($item => {
                        module.push({
                            category: $item.category
                        });
                        data.push({
                            category: $item.category,
                            start: new Date($item.start).getTime(),
                            end: new Date($item.end).getTime(),
                            columnSettings: {
                                fill: $item.color
                            },
                            task: $item.task
                        })
                    })
                    ganttCartView(module, data);
                }
            });

            const ganttCartView = (module, data) => {
                console.log(data)
// Create axes
// https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
                var yAxis = chart.yAxes.push(
                    am5xy.CategoryAxis.new(root, {
                        categoryField: "category",
                        renderer: am5xy.AxisRendererY.new(root, {}),
                        tooltip: am5.Tooltip.new(root, {})
                    })
                );

                yAxis.data.setAll(module);

                var xAxis = chart.xAxes.push(
                    am5xy.DateAxis.new(root, {
                        baseInterval: { timeUnit: "minute", count: 1 },
                        renderer: am5xy.AxisRendererX.new(root, {})
                    })
                );


// Add series
// https://www.amcharts.com/docs/v5/charts/xy-chart/series/
                var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                    xAxis: xAxis,
                    yAxis: yAxis,
                    openValueXField: "start",
                    valueXField: "end",
                    categoryYField: "category",
                    sequencedInterpolation: true
                }));

                series.columns.template.setAll({
                    templateField: "columnSettings",
                    strokeOpacity: 0,
                    tooltipText: "{task}:\n[bold]{openValueX}[/] - [bold]{valueX}[/]"
                });

                series.data.setAll(data);

// Add scrollbars
                chart.set("scrollbarX", am5.Scrollbar.new(root, { orientation: "horizontal" }));

// Make stuff animate on load
// https://www.amcharts.com/docs/v5/concepts/animations/
                series.appear();
                chart.appear(1000, 100);
            }





        }); // end am5.ready()
    </script>
    </script>
@endsection
