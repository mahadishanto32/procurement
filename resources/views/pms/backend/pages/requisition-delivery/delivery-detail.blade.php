<div class="col-md-12">
    <div class="panel panel-info">
        <div class="col-lg-12 invoiceBody">
            <div class="invoice-details mt25 row">
                <div class="well col-6">
                    <ul class="list-unstyled mb0">
                        <li><strong>{{__('Name') }} :</strong> {{($requisitionDelivery->relRequisition->relUsersList->name)?$requisitionDelivery->relRequisition->relUsersList->name:''}}</li>

                        <li><strong>{{__('Unit')}} :</strong> {{($requisitionDelivery->relRequisition->relUsersList->employee->unit->hr_unit_short_name)?$requisitionDelivery->relRequisition->relUsersList->employee->unit->hr_unit_short_name:''}}</li>
                        <li><strong>{{__('Department')}} :</strong> {{($requisitionDelivery->relRequisition->relUsersList->employee->department->hr_department_name)?$requisitionDelivery->relRequisition->relUsersList->employee->department->hr_department_name:''}}</li>
                    </ul>
                </div>
                <div class="col-6">
                    <ul class="list-unstyled mb0 pull-right">

                        <li><strong>{{__('Date')}} :</strong> {{date('d-m-Y',strtotime($requisitionDelivery->relRequisition->requisition_date))}}</li>
                        <li><strong>{{__('Reference No')}}:</strong> {{$requisitionDelivery->relRequisition->reference_no}}</li>
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
                        <th>Delivery Qty</th>
                    </tr>
                </thead>
                <tbody>

                    @if(isset($requisitionDelivery))
                    @php
                    $totalDeliveryQty = 0;
                    $totalStockQty = 0;
                    $stockQty = 0;
                    @endphp
                    @foreach($requisitionDelivery->relDeliveryItems as $key=>$item)
                    @php
                        $stockQty = collect($item->product->relInventoryDetails)->where('hr_unit_id',auth()->user()->employee->as_unit_id)->sum('qty');
                    @endphp
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$item->product->category->name}}</td>
                        <td>{{$item->product->name}} ({{ getProductAttributes($item->product->id) }})</td>
                        <td>{{$item->product->productUnit->unit_name}}</td>
                        <td>{{$stockQty}}</td>
                        <td>{{round($item->delivery_qty)}}</td>

                    </tr>

                    @php
                    $totalDeliveryQty += $item->delivery_qty;
                    $totalStockQty += $stockQty;
                    @endphp

                    @endforeach
                    @endif

                    <tr>
                        <td colspan="4" class="text-right">Total</td>
                        <td colspan="">{{$totalStockQty}}</td>
                        <td colspan="">{{$totalDeliveryQty}}</td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>