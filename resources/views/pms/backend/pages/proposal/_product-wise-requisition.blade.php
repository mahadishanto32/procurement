 @php
 use App\Models\PmsModels\RequisitionDeliveryItem;
 @endphp

 <div class="col-md-12">
    <div class="panel panel-info">
        <div class="col-lg-12 invoiceBody">
            <div class="invoice-details mt25 row">
                <div class="well col-12">
                    <ul class="list-unstyled mb0">
                        <li><strong>{{__('Product Name') }} :</strong> {{$product->name}}</li>
                        <li><strong>{{__('Total Requistion') }} :</strong> {{$product->requisitionItem()->where('is_send','no')->whereIn('requisition_id',$requisitionIds)->count()}}</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="table-responsive style-scroll">
            <table class="table table-striped table-bordered miw-500 dac_table" cellspacing="0" width="100%" id="dataTable">
                <thead>
                    <tr>
                        <th width="5%">{{__('SL No.')}}</th>
                        <th>{{__('REF No')}}</th>
                        <th>{{__('Requisition By')}}</th>
                        <th>{{__('Date')}}</th>
                        <th class="text-center">{{__('Items')}}</th>
                        <th class="text-center">{{__('Requisition Qty')}}</th>
                        <th class="text-center">{{__('Approved Qty')}}</th>
                        <th class="text-center">{{__('RFP Qty')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($items[0]))
                    @php 
                    $totalSumOfSendRFP= 0;
                    @endphp
                    @foreach($items as $key=> $item)
                    @php
                    $sumOfSendRFP=RequisitionDeliveryItem::where('product_id',$item->product_id)->whereHas('relRequisitionDelivery.relRequisition', function($query){
                        return $query->where('status', 1)->where('request_status','send_rfp')
                        ->whereHas('requisitionItems', function($query2){
                            $query2->where('is_send','no');
                        });
                    })->sum('delivery_qty');

                    $totalSumOfSendRFP +=$item->qty-$sumOfSendRFP;
                    @endphp
                    <tr>
                        <td>
                            {{$key+1}}
                        </td>
                        <td>{{$item->requisition->reference_no}}</td>
                        <td>{{$item->requisition->relUsersList->name}}</td>
                        <td>
                            {{date("Y-m-d", strtotime($item->requisition->requisition_date))}}
                        </td>
                        <td>{{$product->name}} ({{ getProductAttributes($product->id) }})</td>
                        <td>{{number_format($item->requisition_qty,0)}}</td>
                        <td>{{$item->qty}}</td>
                        <td>{{($sumOfSendRFP >0)?$item->qty-$sumOfSendRFP:$item->qty}}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="5" class="text-right"><strong>Total</strong></td>
                        <td>{{$items->sum('requisition_qty')}}</td>
                        <td>{{$items->sum('qty')}}</td>
                        <td>{{($sumOfSendRFP >0)?$totalSumOfSendRFP:$items->sum('qty')}}</td>
                    </tr>

                    @endif
                </tbody>
            </table>

        </div>
    </div>
</div>