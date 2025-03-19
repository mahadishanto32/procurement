 <div class="table-responsive">
    <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
        <thead>
            <tr>
                <th>{{__('SL No.')}}</th>
                <th>{{__('P.O Reference')}}</th>
                <th>{{__('P.O Date')}}</th>
                <th>{{__('Challan No')}}</th>
                <th>{{__('GRN Reference')}}</th>
                <th>{{__('GRN Date')}}</th>
                <th>{{__('Po Qty')}}</th>
                <th>{{__('Received Qty')}}</th>
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
            @if($grn->received_status==$received_status)
            <tr>

                <td>{{ ($purchaseOrder->currentpage()-1) * $purchaseOrder->perpage() + $pkey + 1 }}</td>

                <td>
                    <a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="{{route('pms.purchase.order-list.show',$grn->relPurchaseOrder->id)}}" data-title="Purchase Order Details">{{$grn->relPurchaseOrder->reference_no}}
                    </a>
                </td>
                

                <td>
                    {{date('d-M-Y',strtotime($grn->relPurchaseOrder->po_date))}}
                </td>
                <td>{{$grn->challan}}</td>
                <td>
                    <a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="{{route('pms.grn.grn-process.show',$grn->id)}}" data-title="GRN Details">{{$grn->reference_no}}
                    </a>
                </td>

                <td>
                    {{date('d-M-Y',strtotime($grn->received_date))}}
                </td>


                <td>{{$grn->relPurchaseOrder->relPurchaseOrderItems->sum('qty')}}</td>
                <td>{{$grn->relGoodsReceivedItems->sum('qty')}}</td>


                <td class="capitalize">{{$grn->received_status}}</td>
               
                @can('quality-ensure')
                <td class="text-center">
                 @if($grn->relGoodsReceivedItems()->whereIn('quality_ensure',['pending'])->count() > 0)
                 <a href="{{route('pms.quality.ensure.check',$grn->id)}}" title="Quality Ensure" class="btn btn-success btn-sm"><i class="las la-check-circle"> {{ __('Quality Ensure')}}</i></a>
                 @endif
             </td>
             @endcan
         </tr>
         @elseif(empty($received_status))
         <tr>
            @if($rkey == 0)
            <td rowspan="{{ $po->relGoodReceiveNote->count() }}">{{ ($purchaseOrder->currentpage()-1) * $purchaseOrder->perpage() + $pkey + 1 }}</td>

            <td rowspan="{{ $po->relGoodReceiveNote->count() }}">
                <a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="{{route('pms.purchase.order-list.show',$grn->relPurchaseOrder->id)}}" data-title="Purchase Order Details">{{$grn->relPurchaseOrder->reference_no}}
                </a>
            </td>
            <td rowspan="{{ $po->relGoodReceiveNote->count() }}">
                {{date('d-M-Y',strtotime($po->po_date))}}
            </td>
            @endif

            <td>
                <a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="{{route('pms.grn.grn-process.show',$grn->id)}}" data-title="GRN Details">{{$grn->reference_no}}
                </a>
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

            <td class="capitalize">{{$grn->received_status}}</td>
            <td class="text-center">
                <div class="btn-group">

                    @if($grn->is_supplier_rating=='no')
                    <button class="btn dropdown-toggle" data-toggle="dropdown">
                        <span id="statusName{{$grn->id}}" title="Click Here To Supplier Review">
                            {{ __('No')}}
                        </span>
                    </button>
                    @elseif($grn->is_supplier_rating=='yes')
                    {{ __('Yes')}}
                    @endif
                    @if($grn->is_supplier_rating=='no')
                    <ul class="dropdown-menu">
                        <li>
                            <a  href="{{url('pms/supplier/rating/'.$grn->relPurchaseOrder->relQuotation->supplier_id.'/'.$grn->id)}}" title="Click Here To Supplier Review" target="_blank" data-id="{{$grn->id}}" data-status="active">{{ __('Give Supplier Rating')}}</a>
                        </li>
                    </ul>
                    @endif
                </div>
            </td>
            @can('quality-ensure')
            <td class="text-center">
               @if($grn->relGoodsReceivedItems()->whereIn('quality_ensure',['pending'])->count() > 0)
               <a href="{{route('pms.quality.ensure.check',$grn->id)}}" title="Quality Ensure" class="btn btn-success btn-sm"><i class="las la-check-circle"> {{ __('Quality Ensure')}}</i></a>
               @endif
           </td>
           @endcan
       </tr>
       @endif
       @endforeach
       @endif
       @endforeach
       @endif
   </tbody>
</table>
<div class="col-12 py-2">
    @if(count($purchaseOrder)>0)
    <ul class="searchPagination">
        {{$purchaseOrder->links()}}
    </ul>

    @endif
</div>
</div>