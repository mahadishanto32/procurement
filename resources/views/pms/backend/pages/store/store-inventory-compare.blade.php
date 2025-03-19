<div class="col-md-12">
    <div class="panel panel-info">
        <div class="col-lg-12 invoiceBody">
            <div class="invoice-details mt25 row">
                <div class="well col-6">
                    <ul class="list-unstyled mb0">
                        <li><strong>{{__('Name') }} :</strong> {{($requisition->relUsersList->name)?$requisition->relUsersList->name:''}}</li>

                        <li><strong>{{__('Unit')}} :</strong> {{($requisition->relUsersList->employee->unit->hr_unit_short_name)?$requisition->relUsersList->employee->unit->hr_unit_short_name:''}}</li>
                        <li><strong>{{__('Department')}} :</strong> {{($requisition->relUsersList->employee->department->hr_department_name)?$requisition->relUsersList->employee->department->hr_department_name:''}}</li>
                    </ul>
                </div>
                <div class="col-6">
                    <ul class="list-unstyled mb0 pull-right">

                        <li><strong>{{__('Date')}} :</strong> {{date('d-m-Y',strtotime($requisition->requisition_date))}}</li>
                        <li><strong>{{__('Reference No')}}:</strong> {{$requisition->reference_no}}</li>
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
                        <th>Unit</th>
                        <th>Stock Qty</th>
                        <th>Requisition Qty</th>
                        @if($requisition->status==1)
                        <th>Approved Qty</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if(isset($requisition))
                    @php 
                    $totalStockQty = 0;
                    $totalRequisitionQty  = 0;
                    $totalApprovedQty = 0;
                    @endphp
                    @foreach($requisition->items as $key=>$item)
                    @php 
                        $stockQty = collect($item->product->relInventoryDetails)->where('hr_unit_id',auth()->user()->employee->as_unit_id)->sum('qty');
                    @endphp
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$item->product->category->name}}</td>
                        <td>{{$item->product->name}} ({{ getProductAttributes($item->product->id) }})</td>
                        <td>{{$item->product->productUnit->unit_name}}</td>
                        <td>{{$stockQty}}</td>
                        <td>{{number_format($item->requisition_qty,0)}}</td>
                        @if($requisition->status==1)
                        <td>{{$item->qty}}</td>
                        @endif
                    </tr>

                    @php 

                    $totalStockQty += $stockQty;
                    $totalRequisitionQty += $item->requisition_qty;
                    $totalApprovedQty += $item->qty;
                    @endphp
                    @endforeach
                    @endif

                    <tr>
                        <td colspan="4" class="text-right">Total</td>
                        
                        <td colspan="">{{$totalStockQty}}</td>
                        
                        <td colspan="">{{$totalRequisitionQty}}</td>
                        
                        <td colspan="">{{$totalApprovedQty}}</td>


                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>