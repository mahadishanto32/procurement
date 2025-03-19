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
@php
use \App\Models\PmsModels\Grn\GoodsReceivedItem;
use \App\Models\PmsModels\Purchase\PurchaseOrderAttachment;
@endphp
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="{{  route('pms.dashboard') }}">{{ __('Home') }}</a>
                </li>
                <li><a href="#">PMS</a></li>
                <li class="active">{{__($title)}}</li>
                <li class="top-nav-btn">
                    <a href="javascript:history.back()" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="panel">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>{{__('SL No.')}}</th>
                                    <th style="width:15% !important">{{__('P.O Reference')}}</th>
                                    <th style="width:10% !important">{{__('P.O Date')}}</th>
                                    <th>{{__('GRN Reference')}}</th>
                                    <th>{{__('GRN Date')}}</th>
                                    <th>{{__('Challan No')}}</th>
                                    <th>{{__('Challan File')}}</th>
                                    <th>{{__('Po Qty')}}</th>
                                    <th>{{__('GRN Qty')}}</th>
                                    <th>{{__('GRN Amount')}}</th>
                                    <th class="text-center">{{__('Bill Amount')}}</th>
                                    <th class="text-center">{{__('Bill Number')}}</th>
                                    <th>{{__('Receive Status')}}</th>
                                    <th class="text-center">{{__('Attachment')}}</th>
                                    <th class="text-center">{{__('Invoice')}}</th>
                                    <th class="text-center">{{__('Vat Chalan')}}</th>
                                    <th class="text-center">{{__('Option')}}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if(isset($purchaseOrder))
                                @if($purchaseOrder->relGoodReceiveNote->count() > 0)
                                @foreach($purchaseOrder->relGoodReceiveNote as $key=>$grn)
                                @php 
                                $goodsReceiveItemsId=GoodsReceivedItem::where('goods_received_note_id',$grn->id)->pluck('id')->all();
                                $po_attachment=PurchaseOrderAttachment::where('purchase_order_id',$purchaseOrder->id)
                                ->where('goods_received_note_id',$grn->id)
                                ->where('bill_type','grn')
                                ->first();
                                @endphp
                                <tr>
                                    <td>{{$key+1}}</td>
                                    @if($key==0)
                                    <td rowspan="{{$purchaseOrder->relGoodReceiveNote->count()}}">
                                        <a href="javascript:void(0)" class="btn btn-link showBillPODetails" data-src="{{route('pms.purchase.order-list.show',$purchaseOrder->id)}}" data-title="Purchase Order Details">{{$purchaseOrder->reference_no}}
                                        </a>
                                    </td>
                                    @endif

                                    <td>
                                        {{date('d-M-Y',strtotime($purchaseOrder->po_date))}}
                                    </td>

                                    <td>
                                        <a href="javascript:void(0)" class="btn btn-link showBillPODetails" data-src="{{route('pms.grn.grn-process.show',$grn->id)}}" data-title="GRN Details">{{$grn->grn_reference_no}}
                                        </a>
                                    </td>

                                    <td>
                                        {{date('d-M-Y',strtotime($grn->received_date))}}
                                    </td>
                                    <td>{{$grn->challan}}</td>
                                    <td>
                                        @if(!empty($grn->challan_file))
                                        <a href="{{asset($grn->challan_file)}}" class="btn btn-info btn-sm">
                                            <i class="las la-download"></i>Download</a>
                                            @endif
                                        </td>
                                        <td>{{$purchaseOrder->relPurchaseOrderItems->sum('qty')}}</td>
                                        <td>{{$grn->relGoodsReceivedItems->sum('qty')}}</td>
                                        
                                        <td>
                                            @if($goodsReceiveItemsId)
                                            {{number_format($purchaseOrder->relGoodsReceivedItemStockIn()
                                                ->whereIn('goods_received_item_id',$goodsReceiveItemsId)
                                                ->where('is_grn_complete','yes')
                                                ->sum('total_amount'),2)}}
                                                @endif
                                            </td>
                                            

                                            <td class="text-center">{{isset($po_attachment)?number_format($po_attachment->bill_amount,2):'Not Updated Yet.'}}</td>
                                            <td>{{ isset($po_attachment->bill_number) ? $po_attachment->bill_number : '' }}</td>
                                            <td class="capitalize">{{$grn->received_status}}</td>

                                            <td class="text-center">

                                                @if(checkPoAttachment($purchaseOrder->id) && checkPoGrnAttachment($purchaseOrder->id,$grn->id))

                                                <a href="javascript:void(0)" class="btn btn-info btn-xs UploadPOAttachment" grn-amount="{{$purchaseOrder->relGoodsReceivedItemStockIn()
                                                    ->whereIn('goods_received_item_id',$goodsReceiveItemsId)
                                                    ->where('is_grn_complete','yes')
                                                    ->sum('total_amount')}}" data-id="{{$grn->id}}"><i class="las la-upload"></i>Upload
                                                </a>
                                                @endif
                                                
                                                
                                                
                                            </td>
                                            <td>
                                                @if(isset($po_attachment) && isset($po_attachment->invoice_file))
                                                <a href="{{asset($po_attachment->invoice_file)}}" target="__blank" class="btn btn-success btn-xs">
                                                    <i class="las la-file-invoice"></i>
                                                </a>
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($po_attachment) && isset($po_attachment->vat_challan_file))
                                                <a href="{{asset($po_attachment->vat_challan_file)}}" class="btn btn-success btn-xs" target="__blank">
                                                    <i class="las la-money-bill-wave-alt"></i>
                                                </a>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(!empty($po_attachment->status) &&  !empty($po_attachment->remarks))
                                                <a  po-attachment="{{strip_tags($po_attachment->remarks)}}" class="viewRemarks btn btn-success btn-xs"><i class="las la-eye"></i></a>
                                                @endif
                                            </td>
                                            
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td></td>
                                        </tr>
                                        @endif
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
            <div class="modal-dialog modal-xl">
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
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Purchase Order Attachment</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    {!! Form::open(['route' => 'pms.billing-audit.grn.attachment.upload',  'files'=> true, 'id'=>'', 'class' => 'form-horizontal']) !!}

                    <div class="modal-body">
                        <div class="row pl-3 pr-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('bill_number', 'Bill Number', array('class' => 'col-form-label')) !!}
                                    <input type="text" name="bill_number" class="form-control rounded" id="bill_number" placeholder="Enter Bill Number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('bill_amount', 'Bill Amount', array('class' => 'col-form-label')) !!}
                                    <input type="text" onkeypress="return isNumberKey(event)" name="bill_amount" class="form-control rounded" id="bill_amount" placeholder="Enter grn amount" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-line">
                                    {!! Form::label('invoice_file', 'Invoice File (Supported format ::jpeg,jpg,png,gif,pdf & file size max :: 5MB)', array('class' => 'col-form-label')) !!}

                                    <div style="position:relative;">
                                        <a class='btn btn-primary btn-xs font-10' href='javascript:;'>
                                            Choose File...
                                            <input name="invoice_file" type="file" style='position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;' size="40" onchange='$("#upload-file-info").html($(this).val());'>
                                        </a>
                                        &nbsp;
                                        <span class='label label-info' id="upload-file-info"></span>
                                    </div>
                                </div>
                            </div> 
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-line">

                                    {!! Form::label('vat_challan_file', 'Vat Challan No (Supported format ::jpeg,jpg,png,gif,pdf & file size max :: 5MB)', array('class' => 'col-form-label')) !!}
                                    <div style="position:relative;">
                                        <a class='btn btn-success btn-xs font-10' href='javascript:;'>
                                            Choose File...
                                            <input name="vat_challan_file" type="file" style='position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;' size="40" onchange='$("#upload-vat-file-info").html($(this).val());'>
                                        </a>
                                        &nbsp;
                                        <span class='label label-success' id="upload-vat-file-info"></span>
                                    </div>
                                </div>
                            </div> 
                        </div>

                        <input type="hidden" readonly required value="{{$purchaseOrder->id}}" name="purchase_order_id" id="purchase_order_id">
                        <input type="hidden" readonly required  name="goods_received_note_id" id="goods_received_note_id">
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>

        @endsection
        @section('page-script')
        <script>
            (function ($) {
                "use script";

                const showBillPODetails = () => {
                    $('.showBillPODetails').on('click', function () {

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
                showBillPODetails();

                const UploadPOAttachment = () => {
                    $('.UploadPOAttachment').on('click', function () {

                        let id = $(this).attr('data-id');
                        let grnAmount = $(this).attr('grn-amount');
                        $('#goods_received_note_id').val(id);
                        $('#bill_amount').val(parseFloat(grnAmount));

                        return $('#PurchaseOrderAttachmentModal').modal('show')
                        .on('hidden.bs.modal', function (e) {

                            let form = document.querySelector('#PurchaseOrderAttachmentModal').querySelector('form').reset();
                        });
                    });
                };
                UploadPOAttachment();
                const viewRemarks = () => {
                    $('.viewRemarks').on('click', function () {

                        let model = $(this).attr('po-attachment');

                        $('#POdetailsModel').find('#body').html(model);
                        $('#POdetailsModel').find('.modal-title').html(`Note From Audit`);
                        $('#POdetailsModel').modal('show');
                        
                    });
                };
                viewRemarks();

            })(jQuery);

            function isNumberKey(evt){
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
                {
                    return false;
                }
                return true;
            }
        </script>
        @endsection