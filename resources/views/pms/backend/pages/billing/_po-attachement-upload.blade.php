<div class="col-md-12">
    <div class="form-group">
        <div class="table-responsive ">
            <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                <thead>
                    <tr>
                        <th>{{__('GRN Reference')}}</th>
                        <th>{{__('GRN Date')}}</th>
                        <th>{{__('Challan No')}}</th>
                        <th>{{__('Po Qty')}}</th>
                        <th>{{__('GRN Qty')}}</th>
                        <th>{{__('GRN Amount')}}</th>
                        <th class="text-center">{{__('Bill Amount')}}</th>
                        <th>{{__('Receive Status')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $bill_amount = 0;
                    @endphp
                    @if($po->relGoodReceiveNote->count() > 0)
                    @foreach($po->relGoodReceiveNote as $key=>$grn)
                    @php 
                    $po_attachment=\App\Models\PmsModels\Purchase\PurchaseOrderAttachment::where('purchase_order_id',$po->id)
                    ->where('goods_received_note_id',$grn->id)
                    ->where('bill_type','grn')
                    ->first();
                    @endphp
                    @if(!isset($po_attachment->id))
                    @php 
                    $goodsReceiveItemsId=\App\Models\PmsModels\Grn\GoodsReceivedItem::where('goods_received_note_id',$grn->id)
                    ->pluck('id')
                    ->all();
                    $grn_amount = $po->relGoodsReceivedItemStockIn()
                    ->whereIn('goods_received_item_id',$goodsReceiveItemsId)
                    ->where('is_grn_complete','yes')
                    ->sum('total_amount');
                    $bill_amount = $bill_amount+$grn_amount;
                    @endphp
                    <tr>
                        <td>
                            <a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="{{route('pms.grn.grn-process.show',$grn->id)}}" data-title="GRN Details">{{$grn->reference_no}}
                            </a>
                        </td>
                        <td>
                            {{date('d-M-Y',strtotime($grn->received_date))}}
                        </td>
                        <td>
                            {{$grn->challan}}
                        </td>
                        <td>{{$po->relPurchaseOrderItems->sum('qty')}}</td>
                        <td>{{$grn->relGoodsReceivedItems->sum('qty')}}</td>

                        <td class="text-right">
                            @if($goodsReceiveItemsId)
                            {{$grn_amount}}
                            @endif
                        </td>
                        <td class="text-center">Not Updated Yet</td>
                        <td class="capitalize">{{$grn->received_status}}</td>
                    </tr>
                    @endif
                    @endforeach
                    @endif
                    <tr>
                        <td colspan="5" class="text-right"><strong>Total GRN Amount:</strong></td>
                        <td class="text-right">{{ $bill_amount }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="bill_number"><strong>Bill Number</strong></label>
                <input type="text" name="bill_number" value="{{ isset($po_bill->bill_number) ? $po_bill->bill_number : '' }}" class="form-control">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="bill_amount"><strong>Bill Amount</strong></label>
                <input type="text" name="bill_amount" value="{{ isset($po_bill->bill_amount) ? $po_bill->bill_amount : $bill_amount }}" class="form-control" readonly>
            </div>
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

<input type="hidden" readonly required name="purchase_order_id" id="purchase_order_id" value="{{ $po->id }}">