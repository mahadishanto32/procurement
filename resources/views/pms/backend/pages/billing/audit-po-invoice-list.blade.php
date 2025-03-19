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
                <li><a href="#">PMS</a></li>
                <li><a href="#">Bill Manage</a></li>
                <li class="active">{{__($title)}}</li>
                <li class="top-nav-btn">
                    <a href="javascript:history.back()" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>
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
                                    <th>{{__('GRN Reference')}}</th>
                                    <th>{{__('GRN Date')}}</th>
                                    <th>{{__('Challan No')}}</th>
                                    <th>{{__('Challan File')}}</th>
                                    <th>{{__('Po Qty')}}</th>
                                    <th>{{__('GRN Qty')}}</th>
                                    <th>{{__('GRN Amount')}}</th>
                                    <th class="text-center">{{__('Bill Amount')}}</th>
                                    <th>{{__('Receive Status')}}</th>
                                    <th class="text-center">{{__('Invoice')}}</th>
                                    <th class="text-center">{{__('Vat Challan')}}</th>
                                    <th class="text-center">{{__('Approved')}}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if(isset($purchaseOrder))
                                @if($purchaseOrder->relGoodReceiveNote->count() > 0)
                                @foreach($purchaseOrder->relGoodReceiveNote as $key=>$grn)
                                @php
                                $poLedgers = $ledger->whereHas('relSupplierPayment', function($query) use($grn){
                                    return $query->where('purchase_order_id', $grn->purchase_order_id)
                                    ->where('bill_type', 'po')
                                    ->where('status', 'approved');
                                })->count();
                                $grnLedgers = $ledger->whereHas('relSupplierPayment', function($query) use($grn){
                                    return $query->where('purchase_order_id', $grn->purchase_order_id)
                                    ->where('goods_received_note_id', $grn->id)
                                    ->where('bill_type', 'grn')
                                    ->where('status', 'approved');
                                })->count();
                                @endphp
                                <tr>
                                    <td>{{$key+1}}</td>
                                    @if($key==0)
                                    <td rowspan="{{$purchaseOrder->relGoodReceiveNote->count()}}">
                                        <a href="javascript:void(0)" class="btn btn-link showAuditPODetails" data-src="{{route('pms.purchase.order-list.show',$purchaseOrder->id)}}" data-title="Purchase Order Details">{{$purchaseOrder->reference_no}}
                                        </a>
                                    </td>
                                    @endif

                                    <td>
                                        {{date('d-M-Y',strtotime($purchaseOrder->po_date))}}
                                    </td>

                                    <td>
                                        <a href="javascript:void(0)" class="btn btn-link showAuditPODetails" data-src="{{route('pms.grn.grn-process.show',$grn->id)}}" data-title="GRN Details">{{$grn->grn_reference_no}}
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
                                        <?php 
                                        $goodsReceiveItemsId=$goodsReceivedItem->where('goods_received_note_id',$grn->id)
                                        ->pluck('id')
                                        ->all();
                                    ?>
                                    <td>
                                        @if($goodsReceiveItemsId)
                                        {{number_format($purchaseOrder->relGoodsReceivedItemStockIn()
                                            ->whereIn('goods_received_item_id',$goodsReceiveItemsId)
                                            ->where('is_grn_complete','yes')
                                            ->sum('total_amount'),2)}}
                                            @endif
                                        </td>
                                        <?php 
                                        $po_attachment=\App\Models\PmsModels\Purchase\PurchaseOrderAttachment::where('purchase_order_id',$purchaseOrder->id)
                                        ->where('goods_received_note_id',$grn->id)
                                        ->where('bill_type','grn')
                                        ->first();
                                    ?>

                                    <td class="text-center">{{isset($po_attachment)?number_format($po_attachment->bill_amount,2):'Not Updated Yet.'}}</td>
                                    <td class="capitalize">{{$grn->received_status}}</td>

                                    <td class="text-center">

                                        @if(isset($po_attachment) && isset($po_attachment->invoice_file))
                                        <a href="{{asset($po_attachment->invoice_file)}}" target="__blank" class="btn btn-success btn-xs">
                                            <i class="las la-eye"></i>View Invoice
                                        </a>
                                        @endif
                                        
                                    </td>
                                    <td>
                                        @if(isset($po_attachment) && isset($po_attachment->vat_challan_file))
                                        <a href="{{asset($po_attachment->vat_challan_file)}}" class="btn btn-success btn-xs" target="__blank">
                                            <i class="las la-eye"></i>View Vat
                                        </a>
                                        @endif
                                    </td>

                                    <td>
                                        @if(isset($po_attachment->status)&& $grnLedgers == 0) 
                                        <div class="form-group">


                                            <select class="changeStatus form-control" style="width: 100%" bill-type="grn" grn-id="{{$grn->id}}" po-id="{{$purchaseOrder->id}}">


                                                <option {{ isset($po_attachment->status) && $po_attachment->status === 'pending'?'selected':'' }} value="pending">Pending</option>
                                                <option {{ isset($po_attachment->status) && $po_attachment->status === 'approved'?'selected':'' }} value="approved">Approved</option>

                                                <option {{ isset($po_attachment->status) && $po_attachment->status === 'halt'?'selected':'' }} value="halt">Halt</option>

                                            </select>
                                        </div>
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
                        <input type="hidden" readonly required name="grn_id" id="grnId">
                        <input type="hidden" readonly required name="status" id="PoStatus">
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

        const showAuditPODetails = () => {
            $('.showAuditPODetails').on('click', function () {

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
        showAuditPODetails();

        const UploadPOAttachment = () => {
            $('.UploadPOAttachment').on('click', function () {

                let id = $(this).attr('data-id');
                $('#goods_received_note_id').val(id);

                return $('#PurchaseOrderAttachmentModal').modal('show')
                .on('hidden.bs.modal', function (e) {
                    let form = document.querySelector('#PurchaseOrderAttachmentModal').querySelector('form').reset();
                });
            });
        };
        UploadPOAttachment();

        const isNumberKey =(evt) => {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
            {
                return false;
            }
            return true;
        };

        const changeStatus = () => {
            $('.changeStatus').on('change', function () {

                let billType = $(this).attr('bill-type');
                let grnId = $(this).attr('grn-id');
                let poId = $(this).attr('po-id');
                let status = $(this).val();

                $('#billType').val(billType);
                $('#poId').val(poId);
                $('#grnId').val(grnId);
                $('#PoStatus').val(status);

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