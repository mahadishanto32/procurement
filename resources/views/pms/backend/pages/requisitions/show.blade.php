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
                        @can('requisition-acknowledge')
                        <th>Stock Qty</th>
                        @endcan
                        <th>Requisition Qty</th>
                        @if($requisition->status==1)
                        <th>Approved Qty</th>
                        @endif

                    </tr>
                </thead>

                <tbody>
                    @php 
                    $total_stock_qty = 0;
                    $total_requisition_qty = 0;
                    $total_approved_qty = 0;
                    @endphp
                    @forelse($requisition->items as $key=>$item)

                    <tr>
                        <td>{{$key+1}}</td>

                        <td>{{$item->product->category->name}}</td>
                        <td>{{$item->product->name}} ({{ getProductAttributes($item->product_id) }})</td>
                        @can('requisition-acknowledge')
                        <td>{{isset($item->product->relInventorySummary->qty)?$item->product->relInventorySummary->qty:0}}</td>
                        @endcan
                        <td>{{number_format($item->requisition_qty,0)}}</td>
                        @if($requisition->status==1)
                        <td>{{$item->qty}}</td>
                        @endif
                    </tr>
                    @can('requisition-acknowledge')
                    @php

                    $total_stock_qty += isset($item->product->relInventorySummary->qty)?$item->product->relInventorySummary->qty:0;

                    @endphp
                    @endcan
                    @php 
                    $total_requisition_qty += $item->requisition_qty;
                    $total_approved_qty += $item->qty;
                    @endphp
                    @empty

                    @endforelse
                    <tr>
                        <td colspan="3" class="text-right">Total</td>
                        @can('requisition-acknowledge')
                        <td colspan="">{{$total_stock_qty}}</td>
                        @endcan
                        <td colspan="">{{$total_requisition_qty}}</td>
                        @if($requisition->status==1)
                        <td colspan="">{{$total_approved_qty}}</td>
                        @endif

                    </tr>
                </tbody>
            </table>
            <div>
                <strong> Notes: </strong>
                {{$requisition->remarks}}
            </div>
            @if($requisition->status==2 && !empty($requisition->admin_remark))
            <div>
                <strong> Holding Reason: </strong>
                {!!$requisition->admin_remark!!}
            </div>

            @endif

        </div>
    </div>
</div>