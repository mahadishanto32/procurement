<table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
    <thead>
        <tr>
            <th width="5%">{{__('SL No.')}}</th>
            <th>{{__('P.O. Date')}}</th>
            <th>{{__('Supplier')}}</th>
            <th>{{__('Reference No')}}</th>
            <th>{{__('Quotation Ref No')}}</th>
            <th>{{__('P.O Qty')}}</th>
            <th>{{__('GRN Qty')}}</th>
            <th>{{__('Total Price')}}</th>
            <th>{{__('Discount')}}</th>
            <th>{{__('Vat')}}</th>
            <th>{{__('Gross Price')}}</th>
            <th>{{__('BarCode')}}</th>
            <th class="text-center">{{__('Option')}}</th>
        </tr>
    </thead>
    <tbody >
        @if(count($data)>0)
        @foreach($data as $key=> $values)
        <tr>
            <td>{{ ($data->currentpage()-1) * $data->perpage() + $key + 1 }}</td>
            <td>{{date('d-m-Y',strtotime($values->po_date))}}</td>
            <td>{{$values->relQuotation?$values->relQuotation->relSuppliers->name:''}}</td>
            <td> <a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="{{route('pms.purchase.order-list.show',$values->id)}}">{{$values->reference_no}}</a></td>
            <td>{{$values->relQuotation?$values->relQuotation->reference_no:''}}</td>

            <td>{{$values->relPurchaseOrderItems->sum('qty')}}</td>
            <td>{{$values->total_grn_qty}}</td>

            <td>{{$values->total_price}}</td>
            <td>{{$values->discount}}</td>
            <td>{{$values->vat}} %</td>
            <td>{{$values->gross_price}}</td>
            <td>    
                
                <img src="data:image/png;base64,{!!DNS1D::getBarcodePNG($values->reference_no, 'C39',1,33) !!}" alt="barcode" />
            </td>


            <td class="text-center">

                @if($values->relPurchaseOrderItems->sum('qty')==$values->total_grn_qty??0)

                <button class="btn btn-default">{{__('Full Received')}}</button>
                @else
                <a href="{{ route('pms.grn.grn-list.createGRN',$values->id) }}" data-toggle="tooltip" title="Click here to generate GRN">
                    <button type="button" class="btn btn-sm btn-primary">{{ __('GRN') }}</button>
                </a>
                @endif

            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
<div class="col-12 py-2">
    @if(count($data)>0)
    <ul class="searchPagination">
        {{$data->links()}}
    </ul>

    @endif
</div>