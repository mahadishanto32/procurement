@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
<style type="text/css">
    .list-unstyled .ratings {
        display: none;
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
                   
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="panel panel-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <p class="mb-1 font-weight-bold"><label for="from_date">{{ __('From Date') }}:</label></p>
                        <div class="input-group input-group-md mb-3 d-">
                            <input type="text" name="from_date" id="from_date" class="search-datepicker form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required readonly value="{{ old('from_date')?old('from_date'):date("d-m-Y", strtotime(date('Y-m-01'))) }}">
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <p class="mb-1 font-weight-bold"><label for="to_date">{{ __('To Date') }}:</label></p>
                        <div class="input-group input-group-md mb-3 d-">
                            <input type="text" name="to_date" id="to_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required readonly value="{{ old('to_date')?old('to_date'):date('d-m-Y') }}">
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <p class="mb-1 font-weight-bold"><label for="received_status">{{ __('Received Status') }}:</label></p>
                        <div class="input-group input-group-md mb-3 d-">
                            <select name="received_status" id="received_status" class="form-control rounded">
                                <option value="{{ null }}">{{ __('Select One') }}</option>
                                <option value="full">Full Received</option>
                                <option value="partial">Partial Received</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <p class="mb-1 font-weight-bold"><label for="searchGRNList"></label></p>
                        <div class="input-group input-group-md">
                            <a href="javascript:void(0)" class="btn btn-success rounded mt-8" data-src="{{route('pms.grn.grn-process.search')}}" id="searchGRNList"> <i class="las la-search"></i>Search</a>
                        </div>
                    </div>
                </div>
            
                <div id="viewResult">


                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>{{__('SL No.')}}</th>
                                    <th>{{__('P.O Reference')}}</th>
                                    <th>{{__('P.O Date')}}</th>
                                    <th>{{__('Chalan No')}}</th>
                                    <th>{{__('Gate-In Reference')}}</th>
                                    <th>{{__('Gate-In Date')}}</th>
                                    <th>{{__('Po Qty')}}</th>
                                    <th>{{__('Gate in Qty')}}</th>
                                    <th>{{__('Receive Status')}}</th>
                                    @can('quality-ensure')
                                    <th>{{__('Quality Ensure')}}</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>

                                @if(count($purchaseOrder)>0)
                                @foreach($purchaseOrder as $pkey=> $po)
                                @if($po->relGoodReceiveNote->count() > 0)
                                @foreach($po->relGoodReceiveNote as $rkey => $grn)
                                <tr>
                                    @if($rkey == 0)
                                    <td rowspan="{{ $po->relGoodReceiveNote->count() }}">{{ ($purchaseOrder->currentpage()-1) * $purchaseOrder->perpage() + $pkey + 1 }}</td>

                                    <td rowspan="{{ $po->relGoodReceiveNote->count() }}">
                                        <a href="javascript:void(0)" class="btn btn-link showGateInPODetails" data-src="{{route('pms.purchase.order-list.show',$grn->relPurchaseOrder->id)}}" data-title="Purchase Order Details">{{$grn->relPurchaseOrder->reference_no}}
                                        </a>
                                    </td>
                                    <td rowspan="{{ $po->relGoodReceiveNote->count() }}">
                                        {{date('d-M-Y',strtotime($po->po_date))}}
                                    </td>
                                    @endif
                                    <td>{{$grn->challan}}</td>
                                    <td>
                                        <a href="javascript:void(0)" class="btn btn-link showGateInPODetails" data-src="{{route('pms.grn.grn-process.show',$grn->id)}}" data-title="Gate-In Details">{{$grn->reference_no}}
                                        </a>
                                        <a class="btn btn-primary btn-xs" href="{{ url('pms/grn/gate-in-slip/'.$po->id.'?grn='.$grn->id) }}" target="_blank"><i class="la la-print"></i></a>
                                    </td>

                                    <td>
                                        {{date('d-M-Y',strtotime($grn->received_date))}}
                                    </td>

                                    @if($rkey == 0)
                                    <td rowspan="{{ $po->relGoodReceiveNote->count() }}">
                                        {{$po->relPurchaseOrderItems->sum('qty')}}
                                    </td>
                                    @endif
                                    <td>{{$grn->relGoodsReceivedItems->sum('qty')}}</td>
                                    <td class="text-center">
                                        @if($grn->received_status == 'partial')
                                            <a class="btn btn-warning btn-xs">Partial Received</a>
                                        @elseif($grn->received_status == 'full')
                                            <a class="btn btn-success btn-xs">Full Received</a>
                                        @else
                                            <a class="btn btn-dark btn-xs">{{ ucwords($grn->received_status) }}</a>
                                        @endif
                                    </td>
                                    @can('quality-ensure')
                                    <td class="text-center">
                                       @if($grn->relGoodsReceivedItems()->whereIn('quality_ensure',['pending'])->count() > 0)
                                       <a href="{{route('pms.quality.ensure.check',$grn->id)}}" title="Quality Ensure" class="btn btn-success btn-sm"><i class="las la-check-circle"> {{ __('Quality Ensure')}}</i></a>
                                       @endif
                                   </td>
                                   @endcan
                               </tr>
                               @endforeach
                               @endif
                               @endforeach
                               @endif
                           </tbody>
                       </table>
                       <div class="col-12 py-2">
                            @if(count($purchaseOrder)>0)
                            <ul>
                                {{$purchaseOrder->links()}}
                            </ul>

                            @endif
                        </div>
                   </div>

               </div>
           </div>
       </div>
   </div>
</div>

<div class="modal" id="POdetailsModel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Purchase Order</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body" id="body">

            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

@endsection
@section('page-script')
<script>
    (function ($) {
        "use script";

        $('#searchGRNList').on('click', function () {

            let from_date=$('#from_date').val();
            let to_date=$('#to_date').val();
            let received_status=$('#received_status').val();

            const searchGRNList = () => {
                let container = document.querySelector('.searchPagination');
                let pageLink = container.querySelectorAll('.page-link');
                Array.from(pageLink).map((item, key) => {
                    item.addEventListener('click', (e)=>{
                        e.preventDefault();
                        let getHref = item.getAttribute('href');
                        showPreloader('block');
                        $.ajax({
                            type: 'post',
                            url: getHref,
                            dataType: "json",
                            data:{_token:'{!! csrf_token() !!}',from_date:from_date,to_date:to_date,received_status:received_status},
                            success:function (data) {
                                if(data.result == 'success'){
                                    showPreloader('none');
                                    $('#viewResult').html(data.body);
                                    searchGRNList();
                                    showPODetails();
                                }else{
                                    showPreloader('none');
                                    notify(data.message,'error');

                                }
                            }
                        });
                    })

                });
            };
            
            if (from_date !='' || to_date !='' || received_status !='') {
                showPreloader('block');
                $.ajax({
                    type: 'post',
                    url: $(this).attr('data-src'),
                    dataType: "json",
                    data:{_token:'{!! csrf_token() !!}',from_date:from_date,to_date:to_date,received_status:received_status},
                    success:function (data) {
                        if(data.result == 'success'){
                            showPreloader('none');
                            $('#viewResult').html(data.body);
                            searchGRNList();
                            showPODetails();
                        }else{
                            showPreloader('none');
                            $('#viewResult').html('<div class="col-md-12"><center>No Data Found!!</center></div>');

                        }
                    }
                });
                return false;
            }else{
                notify('Please enter data first !!','error');
            }
        });


        const showPODetails = () => {
            $('.showGateInPODetails').on('click', function () {

                var modalTitle= $(this).attr('data-title');
                $.ajax({
                    url: $(this).attr('data-src'),
                    type: 'get',
                    dataType: 'json',
                    data: '',
                })
                .done(function(response) {

                    if (response.result=='success') {
                        $('#POdetailsModel .modal-title').html(modalTitle);
                        $('#POdetailsModel').find('#body').html(response.body);
                        $('#POdetailsModel').modal('show');
                    }

                })
                .fail(function(response){
                    notify('Something went wrong!','error');
                });
            });
        }
        showPODetails();

    })(jQuery);
</script>
@endsection