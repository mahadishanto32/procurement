@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
<style type="text/css">
    .col-form-label{
        font-size: 14px;
        font-weight: 600;
    }

    .list-unstyled .ratings {
        display: none;
    }
</style>
@endsection
@section('main-content')
@php
use App\Models\PmsModels\Purchase\PurchaseOrderAttachment;
use App\Models\PmsModels\SupplierLedgers;
@endphp
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
                <li class="active">Bill Manage</li>
                <li class="active">{{__($title)}}</li>

            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <div class="panel-body">
                        <form action="{{ route('pms.billing-audit.po.attachment.list') }}" method="get" accept-charset="utf-8">
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <p class="mb-1 font-weight-bold"><label for="search_text">Enter Search Text</label></p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <input type="text" name="search_text" id="search_text" class="form-control" placeholder="Search Here..." value="{{ request()->has('search_text') ? request()->get('search_text') : '' }}"/>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <p class="mb-1 font-weight-bold"><label for="from_date">{{ __('From Date') }}:</label></p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <input type="text" name="from_date" id="from_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{  request()->has('from_date')? request()->get('from_date'):date("d-m-Y", strtotime(date('Y-m-01'))) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <p class="mb-1 font-weight-bold"><label for="to_date">{{ __('To Date') }}:</label></p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <input type="text" name="to_date" id="to_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{ request()->has('to_date')? request()->get('to_date'):date('d-m-Y') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <p class="mb-1 font-weight-bold"><label for="status">{{ __('Status') }}:</label></p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <select name="status" id="status" class="form-control rounded">
                                            <option value="{{ null }}">{{ __('Select One') }}</option>
                                            @foreach(stringStatusArray() as $key=> $values)
                                            <option {{ request()->get('status')==$key?'selected':'' }} value="{{ $key }}">{{ $values}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6">
                                            <p class="mb-1 font-weight-bold"><label for=""></label></p>
                                            <div class="input-group input-group-md">
                                                <button type="submit" class="btn btn-success btn-block rounded mt-8"><i class="las la-search"></i>&nbsp;Search</button>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <p class="mb-1 font-weight-bold"><label for=""></label></p>
                                            <div class="input-group input-group-md">
                                                <a href="{{ route('pms.billing-audit.po.attachment.list') }}" class="btn btn-danger btn-block rounded mt-8"><i class="las la-times"></i>&nbsp;Clear</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>  
                        </form>                  
                    </div>
                    <table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="{{ $title }}" cellspacing="0" width="100%" id="dataTable">
                        <thead>
                            <tr>
                                <th width="5%">{{__('SL No.')}}</th>
                                <th>{{__('P.O. Date')}}</th>
                                <th>{{__('Supplier')}}</th>
                                <th>{{__('Reference No')}}</th>
                                <th>{{__('P.O Qty')}}</th>
                                <th class="text-center">{{__('GRN Qty')}}</th>
                                <th class="text-center">{{__('Po Amount')}}</th>
                                <th class="text-center">{{__('GRN Amount')}}</th>
                                <th class="text-center">{{__('Bill Amount')}}</th>

                                <th class="text-center">{{__('Status')}}</th>
                                <th class="text-center">{{__('Invoice')}}</th>
                                <th class="text-center">{{__('Vat Challan')}}</th>
                                <th class="text-center">{{__('Option')}}</th>
                                <th class="text-center">{{__('Approved')}}</th>
                            </tr>
                        </thead>
                        <tbody id="viewResult">
                            @if(count($purchase_order)>0)
                            @foreach($purchase_order as $key=> $values)
                            @php
                            $ledgers =SupplierLedgers::whereHas('relSupplierPayment', function($query) use($values){
                                return $query->where('purchase_order_id', $values->id)
                                ->where('bill_type', 'po')
                                ->where('status', 'approved');
                            })->count();
                            @endphp

                            @if(PurchaseOrderAttachment::where('purchase_order_id',$values->id)->sum('bill_amount') > 0)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{date('d-m-Y',strtotime($values->po_date))}}</td>
                                <td>{{$values->relQuotation?$values->relQuotation->relSuppliers->name:''}}</td>
                                <td> <a href="javascript:void(0)" class=" btn-link showPODetails" data-src="{{route('pms.purchase.order-list.show',$values->id)}}">{{$values->reference_no}}</a></td>

                                <td>{{$values->relPurchaseOrderItems->sum('qty')}}</td>
                                <td class="text-center">{{$values->total_grn_qty}}</td>
                                <td class="text-center">{{number_format($values->gross_price,2)}}</td>
                                <td class="text-center">
                                    @if($values->relGoodsReceivedItemStockIn)
                                    
                                    {{number_format($values->relGoodsReceivedItemStockIn()->where('is_grn_complete','yes')->sum('total_amount'),2)}}
                                    @endif
                                </td>

                                <?php 
                                $po_attachment=PurchaseOrderAttachment::where('purchase_order_id',$values->id)->where('bill_type','po')->first();
                            ?>

                            <td class="text-center">{{PurchaseOrderAttachment::where('purchase_order_id',$values->id)->sum('bill_amount') > 0 ? number_format(PurchaseOrderAttachment::where('purchase_order_id',$values->id)->sum('bill_amount'), 2)  : 'Not Updated Yet'}}</td>

                            <td class="text-center text-left">
                                @if($values->relPurchaseOrderItems->sum('qty')==$values->total_grn_qty??0)
                                {{__('Full Received')}}
                                @else
                                {{__('Partial Received')}}
                                @endif
                            </td>
                            <td class="text-center">
                                @if(isset($po_attachment))
                                <a href="{{asset($po_attachment->invoice_file)}}" target="__blank" class="btn btn-success btn-xs">
                                    <i class="las la-file-invoice"></i>
                                </a>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(isset($po_attachment))
                                <a href="{{asset($po_attachment->vat_challan_file)}}" class="btn btn-success btn-xs" target="__blank">
                                    <i class="las la-money-check-alt"></i>
                                </a>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{route('pms.billing-audit.audit.po.invoice.list',$values->id)}}" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Click here to PO Challan List">Challan({{$values->relGoodReceiveNote->count()}})</a>

                                <a target="__blank" href="{{route('pms.billing-audit.po.invoice.print',$values->id)}}" class="btn btn-warning btn-xs"><i class="las la-print"></i></a>
                                
                            </td>
                            <td>
                                @if($ledgers == 0 && $po_attachment)
                                <div class="form-group">
                                    <select class="changeStatus form-control" style="width: 100%" bill-type="po" po-id="{{$values->id}}" id="changeStatus{{$values->id}}">
                                    
                                    <option {{ isset($po_attachment->status) && $po_attachment->status === 'pending'?'selected':'' }} value="pending">Pending</option>
                                    
                                    
                                    <option {{ isset($po_attachment->status) && $po_attachment->status === 'approved'?'selected':'' }} value="approved">Approved</option>

                                    <option {{ isset($po_attachment->status) && $po_attachment->status === 'halt'?'selected':'' }} value="halt">Halt</option>
                                    </select>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endif
                        @endforeach
                        @endif
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-md-12">
                        <div class="la-1x pull-right">
                            @if(count($purchase_order)>0)
                            <ul>
                                {{$purchase_order->links()}}
                            </ul>
                            @endif
                        </div>
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

<div class="modal" id="PurchaseOrderAttachmentModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">PO Attachment Approval</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <form action="{{route('pms.billing-audit.po.invoice.approved')}}" method="POST">
                @csrf
                <div class="modal-body">
                    
                    <div class="form-group">
                        <label for="remarks">Notes :</label>
                        <textarea class="form-control" name="remarks" rows="3" id="remarks" placeholder="Remarks...."></textarea>

                        <input type="hidden" readonly required name="bill_type" id="billType">
                        <input type="hidden" readonly required name="po_id" id="poId">
                        <input type="hidden" readonly required name="status" id="poStatus">
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('page-script')

<script>
    (function ($) {
        "use script";

        const showPODetails = () => {
            $('.showPODetails').on('click', function () {

                $.ajax({
                    url: $(this).attr('data-src'),
                    type: 'get',
                    dataType: 'json',
                    data: '',
                })
                .done(function(response) {

                    if (response.result=='success') {
                        $('#POdetailsModel').find('#body').html(response.body);
                        $('#POdetailsModel').find('.modal-title').html(`Purchase Order Details`);
                        $('#POdetailsModel').modal('show');
                    }

                })
                .fail(function(response){
                    notify('Something went wrong!','error');
                });
            });
        };
        showPODetails();
        
        const changeStatus = () => {
            $('.changeStatus').on('change', function () {

                let billType = $(this).attr('bill-type');
                let poId = $(this).attr('po-id');
                let status = $(this).val();

                $('#billType').val(billType);
                $('#poId').val(poId);
                $('#poStatus').val(status);

                if (status!='autoSelect') {
                    return $('#PurchaseOrderAttachmentModal').modal('show').on('hidden.bs.modal', function (e) {
                        let form = document.querySelector('#PurchaseOrderAttachmentModal').querySelector('form').reset();
                    });
                }
            });
        };
        changeStatus();


    })(jQuery);
</script>

@endsection