<div class="col-md-12">
    <div class="panel panel-info">
        <div class="col-lg-12 invoiceBody">
            <div class="invoice-details mt25 row">
                <div class="well col-6">
                    <strong>Assign to suppliers:</strong>
                    <ul class="list-unstyled mb0">

                        <li><strong>{{__('Name') }} :</strong>
                        {{ $requestProposal->defineToSupplier->pluck('supplier.name')->implode(', ') }}
                        </li>
                    </ul>
                </div>
                <div class="col-6">
                    <ul class="list-unstyled mb0 pull-right">

                        <li><strong>{{__('Date')}} :</strong> {{date('d-m-Y',strtotime($requestProposal->request_date))}}</li>
                        <li><strong>{{__('Reference No')}}:</strong> {{$requestProposal->reference_no}}</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Sl No.</th>
                        <th>Category</th>
                        <th>Product</th>
                        <th>Qty</th>
                    </tr>
                </thead>

                <tbody>
                    @if($requestProposal->requestProposalDetails)
                    @php
                    $requestQty=0;
                    @endphp
                    @foreach($requestProposal->requestProposalDetails as $key=>$requestProposalDetail)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$requestProposalDetail->product->category->name}}</td>
                        <td>{{$requestProposalDetail->product->name}} ({{ getProductAttributes($requestProposalDetail->product_id) }})</td>
                        <td>{{$requestProposalDetail->request_qty}}</td>
                        
                    </tr>
                    @php
                    $requestQty+=$requestProposalDetail->request_qty;
                    @endphp
                    @endforeach
                    @endif
                    <tr>
                        <td colspan="3">Total</td>
                        <td>{{$requestQty}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>