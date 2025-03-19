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
                    {{--<a href="{{ route('pms.grn.grn-process.index') }}" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>--}}
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">

                <div class="panel-body">
                    <div class="table-responsive ">
                        <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>{{__('SL No.')}}</th>
                                    <th>{{__('P.O Reference')}}</th>
                                    <th>{{__('P.O Date')}}</th>
                                    <th>{{__('Challan No')}}</th>
                                    <th>{{__('Gate-In Reference')}}</th>
                                    <th>{{__('Gate-In Date')}}</th>
                                    <th>{{__('Po Qty')}}</th>
                                    <th>{{__('Gate-In Qty')}}</th>
                                    <th>{{__('Receive Status')}}</th>
                                    <th>{{__('Approved Qty')}}</th>
                                    <th>{{__('Option')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(isset($purchaseOrder[0]))
                            @foreach($purchaseOrder as $pkey => $po)
                            @php
                                $goodReceivedNotes = \App\Models\PmsModels\Grn\GoodsReceivedNote::where('purchase_order_id', $po->id)
                                ->whereHas('relGoodsReceivedItems', function($query){
                                    return $query->where('quality_ensure','approved');
                                })->get();
                            @endphp  
                                @if($goodReceivedNotes->count() > 0)
                                @foreach($goodReceivedNotes as $rkey => $grn)
                                <tr>
                                    @if($rkey == 0)
                                    <td rowspan="{{ $goodReceivedNotes->count() }}">{{ ($purchaseOrder->currentpage()-1) * $purchaseOrder->perpage() + $pkey + 1 }}</td>

                                    <td rowspan="{{ $goodReceivedNotes->count() }}">
                                        <a href="javascript:void(0)" class="btn btn-link showQEPODetails" data-src="{{route('pms.purchase.order-list.show',$grn->relPurchaseOrder->id)}}" data-title="Purchase Order Details">{{$grn->relPurchaseOrder->reference_no}}
                                        </a>
                                    </td>
                                    <td rowspan="{{ $goodReceivedNotes->count() }}">
                                        {{date('d-M-Y',strtotime($po->po_date))}}
                                    </td>
                                    @endif
                                    <td>{{$grn->challan}}</td>
                                    <td>
                                        <a href="javascript:void(0)" class="btn btn-link showQEPODetails" data-src="{{route('pms.grn.grn-process.show',$grn->id)}}" data-title="Gate-In Details">{{$grn->reference_no}}
                                        </a>
                                        <a class="btn btn-primary btn-xs" href="{{ url('pms/grn/gate-in-slip/'.$po->id.'?grn='.$grn->id) }}" target="_blank"><i class="la la-print"></i></a>
                                    </td>

                                    <td>
                                        {{date('d-M-Y',strtotime($grn->received_date))}}
                                    </td>


                                    @if($rkey == 0)
                                    <td rowspan="{{ $goodReceivedNotes->count() }}">
                                        {{$po->relPurchaseOrderItems->sum('qty')}}
                                    </td>
                                    @endif
                                    
                                    <td>{{$grn->relGoodsReceivedItems->sum('qty')}}</td>
                                    <td class="capitalize">{{$grn->received_status}}</td>
                                    <td>{{$grn->relGoodsReceivedItems->where('quality_ensure','approved')->sum('qty')}}</td>
                                    <td>
                                        <?php $count= $grn->relGoodsReceivedItems()->where('quality_ensure','approved')->count(); ?>
                                        @if($count > 0)
                                        <a href="{{route('pms.quality.ensure.approved.single.list',$grn->id)}}" class="btn btn-xs btn-info">{{__('Items')}} ({{$count}})</a>
                                        @endif
                                        <a target="__blank" href="{{route('pms.quality.approved.item.print',['id'=>$grn->id,'type'=>'approved'])}}" title="Approved List" class="btn btn-xs btn-success"><i class="las la-print"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            @endforeach
                            @endif

                               
                            </tbody>
                        </table>
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

        const showQEPODetails = () => {
            $('.showQEPODetails').on('click', function () {

                var modalTitle= $(this).attr('data-title');
                $.ajax({
                    url: $(this).attr('data-src'),
                    type: 'get',
                    dataType: 'json',
                    data: '',
                })
                .done(function(response) {

                    if (response.result=='success') {
                        $('#POdetailsModel').find('#body').html(response.body);
                        $('#POdetailsModel').find('.modal-title').html(modalTitle);
                        $('#POdetailsModel').modal('show');
                    }

                })
                .fail(function(response){
                    notify('Something went wrong!','error');
                });
            });
        }
        showQEPODetails();

    })(jQuery);
</script>
@endsection