@extends('hr.layout')
@section('title', 'Cross Analysis')

@section('main-content')
@push('css')
  <style>
    .single-employee-search {
      margin-top: 82px !important;
    }
    .view:hover, .view:hover{
      color: #ccc !important;
      
    }
    .grid_view{

    }
    .view i{
      font-size: 25px;
      border: 1px solid #000;
      border-radius: 3px;
      padding: 0px 3px;
    }
    .view.active i{
      background: linear-gradient(to right,#0db5c8 0,#089bab 100%);
      color: #fff;
      border-color: #089bab;
    }
    .iq-card .iq-card-header {
      margin-bottom: 10px;
      padding: 15px 15px;
      padding-bottom: 0px;
    }
    .modal-h3{
      line-height: 15px !important;
    }
    .select2-container .select2-selection--single, .month-report { height: 30px !important;}
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 30px !important;}
    .apexcharts-toolbar{display: none !important;}
  </style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Reports</a>
                </li>
                <li class="active"> Cross Analysis</li>
            </ul>
        </div>
        <div class="page-content"> 
            
            <div class="row">
                <div class="col">
                  <form role="form" method="get" action="{{ url("hr/reports/employee-cross-analysis-filter-report") }}" id="formReport">
                    @csrf
                    <div class="iq-card" id="result-section">
                      <div class="iq-card-header d-flex mb-0">
                         <div class="iq-header-title w-100">
                            <div class="row">
                              <div style="width: 10%; float: left; margin-left: 15px; margin-top: 2px;">
                                <div id="result-section-btn">
                                  <button type="button" class="btn btn-sm btn-primary hidden-print" onclick="printDiv('report_section')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report" style="margin-top: 3px;"><i class="las la-print"></i> </button>
                                </div>
                              </div>
                              <div class="text-center" style="width: 47%; float: left">
                                
                              </div>

                              <input type="hidden" id="reportFormat" name="report_format" value="1">
                              <div style="width: 40%; float: left">
                                <div class="row">
                                  <div class="col-3 p-0">
                                      &nbsp;
                                  </div>
                                  <div class="col-4 pr-0">
                                    <div class="format">
                                      
                                      <div class="form-group has-float-label select-search-group mb-0">
                                          <?php
                                            
                                            $type = ['as_unit_id'=>'Unit','as_location'=>'Location','as_department_id'=>'Department','as_designation_id'=>'Designation','as_section_id'=>'Section','as_subsection_id'=>'Sub Section'];
                                            if(auth()->user()->hasRole('Super Admin')){
                                              $selectGroup = 'as_unit_id';
                                            }else{
                                              $selectGroup = 'as_department_id';
                                            }
                                          ?>
                                          
                                          {{ Form::select('report_group', $type, 'as_section_id', ['class'=>'form-control capitalize', 'id'=>'reportGroupHead']) }}
                                          <label for="reportGroupHead">Report Format</label>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-5 pl-0">
                                    <div class="text-right">
                                      <a class="btn view no-padding clear-filter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Clear Filter">
                                        <i class="las la-redo-alt" style="color: #f64b4b; border-color:#be7979"></i>
                                      </a>
                                      <a class="btn view no-padding filter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Advanced Filter">
                                        <i class="fa fa-filter"></i>
                                      </a>
                                      <a class="btn view grid_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Summary Report View" id="1">
                                        <i class="las la-th-large"></i>
                                      </a>
                                      <a class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Details Report View" id="0">
                                        <i class="las la-list-ul"></i>
                                      </a>
                                      
                                    </div>
                                  </div>
                                </div>
                                
                              </div>
                            </div>
                         </div>
                      </div>
                      <div class="iq-card-body no-padding">
                        <div class="result-data" id="result-data">
                          
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
{{-- modal employee salary --}}

@section('right-nav')
  @php
    $filter_status = '';
  @endphp
  <hr class="mt-2">
  <div class="row">
    <div class="col-sm-12">
      <div class="form-group has-float-label has-required ">
        <input type="month" class="report_date form-control month-report" id="year-month-from" name="month_from" placeholder=" Month-Year from" required="required" value="{{ $yearMonth }}" max="{{ date('Y-m') }}" autocomplete="off">
        <label for="year-month-from">Month From</label>
      </div>
    </div>
    <div class="col-sm-12">
      <div class="form-group has-float-label has-required ">
        <input type="month" class="report_date form-control month-report" id="year-month-to" name="month_to" placeholder=" Month-Year to" required="required" value="{{ $yearMonth }}" max="{{ date('Y-m') }}" autocomplete="off">
        <label for="year-month-to">Month To</label>
      </div>
    </div>
  </div>
@endsection
  {{--  --}}
@include('common.right-modal')
@include('common.right-navbar')

@push('js')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script src="{{ asset('assets/js/apexcharts.js') }}"></script>
<script type="text/javascript">
  @if(Request::get('unit')) 
    advFilter();
  @endif
  $(document).ready(function() {
    $(".filter").click();
  });
  // $(document).on('click', '.yearly-activity', function() {
  //     let id = $(this).data('id');
  //     let associateId = $(this).data('eaid');
  //     let name = $(this).data('ename');
  //     let designation = $(this).data('edesign');
  //     let yearMonth = $(this).data('yearmonth');
  //     $("#modal-title-right").html(' '+name+' Salary Details');
  //     $('#right_modal_jobcard').modal('show');
  //     $("#content-result-jobcard").html(loaderContent);
  //     $.ajax({
  //         url: '/hr/operation/unit-wise-salary-sheet',
  //         type: "GET",
  //         data: {
  //             as_id: [associateId],
  //             year_month: yearMonth,
  //             sheet:0,
  //             perpage:1
  //         },
  //         type: "GET",
  //         dataType: "json",
  //         success: function(response){
  //             if(response !== 'error'){
  //               setTimeout(function(){
  //                 $("#content-result-jobcard").html(response.view);
  //               }, 1000);
  //             }else{
  //               console.log(response.view);
  //             }
  //         }
  //     });
  // });
  
</script>
@endpush

@endsection