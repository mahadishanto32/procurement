@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
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
                <li>
                    <a href="#">PMS</a>
                </li>
                <li class="active">{{__($title)}}</li>
                <li class="top-nav-btn">
                    <a href="javascript:history.back()" class="btn btn-sm btn-warning text-white" data-toggle="tooltip" title="Back" > <i class="las la-chevron-left"></i>Back</a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <form  method="post" id="updateInventoryForm" action="{{ route('pms.store-manage.store-requistion.submit') }}">
                        @csrf
                        <div class="panel-body">
                            <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {!! Form::label('delivery_date', 'Delivery Date', array('class' => 'mb-1 font-weight-bold')) !!}
                                            {!! Form::text('delivery_date',old('delivery_date',date('d-m-Y')),['id'=>'delivery_date','class' => 'form-control rounded air-datepicker','placeholder'=>'','readonly'=>'readonly']) !!}

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {!! Form::label('requisition_reference_no', 'Requisition Ref No', array('class' => 'mb-1 font-weight-bold')) !!}
                                            
                                            <input type="text" id="requisition_reference_no" name="" class="form-control rounded" readonly value="{{$requisition->reference_no}}">

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {!! Form::label('reference_no', 'Ref No', array('class' => 'mb-1 font-weight-bold')) !!}
                                            {!! Form::text('reference_no',old('reference_no')?old('reference_no'):$refNo,['id'=>'reference_no','required'=>false,'class' => 'form-control rounded','placeholder'=>'Enter Reference No','readonly'=>'readonly']) !!}

                                            @if ($errors->has('reference_no'))
                                            <span class="help-block"><strong class="text-danger">{{ $errors->first('reference_no') }}</strong>
                                            </span>
                                            @endif

                                        </div>
                                    </div>
                                </div>


                            </div><!--end row -->
                            <div class="table-responsive">
                                <h5>Requisition Details</h5>

                                <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>Sl No.</th>
                                            
                                            <th>Category</th>
                                            <th>Product</th>
                                            <th>Stock Qty</th>
                                            <th>Req.Qty</th>
                                            <th>Prv.Dlv. Qty</th>
                                            <th>Dlv.Qty</th>
                                            <th>Left.Qty</th>
                                            <th>Ware House</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if(isset($requisitionItems))
                                        @php 
                                            $total_stock_qty = 0;
                                            $total_requision_qty = 0;
                                        @endphp
                                        @foreach($requisitionItems as $key=>$item)
                                        @if($item->qty!=$item->delivery_qty)
                                        @php
                                            $itemStock = \App\Models\PmsModels\InventoryModels\InventoryDetails::whereIn('warehouse_id', auth()->user()->relUsersWarehouse->pluck('id')->toArray())
                                            ->where([
                                                'category_id' => $item->product->category_id,
                                                'product_id' => $item->product_id,
                                                'hr_unit_id' => (isset(auth()->user()->employee->as_unit_id) ? auth()->user()->employee->as_unit_id : 0),
                                            ])->sum('qty');
                                        @endphp
                                        <tr id="SelectedRow{{$item->product->id}}">
                                            <td>{{$key+1}}</td>
                                            <td>{{$item->product->category->name}}</td>
                                            <td>{{$item->product->name}} ({{ getProductAttributes($item->product_id) }})</td>
                                            <td>{{ $itemStock }}</td>
                                            <td>{{$item->qty}}</td>
                                            <td>{{$item->delivery_qty}}</td>
                                            <td>
                                                <input type="number" name="delivery_qty[{{$item->product->id}}]" id="delivery_qty_{{$item->product->id}}" class="form-control deliveryQty" min="0" max="{{$item->qty<$itemStock ? $item->qty : $itemStock}}" data-id="{{$item->product->id}}" value="0">
                                            </td>
                                            <td>
                                                <input type="hidden" value="{{$item->qty-$item->delivery_qty}}" id="leftQty_{{$item->product->id}}">
                                                <span id="leftQtyAfterSubTract_{{$item->product->id}}">{{$item->qty-$item->delivery_qty}}</span>
                                            </td>
                                            <td>
                                                <select style="width:200px !important" class="form-control not-select2 warehouse" name="warehouse_id[{{$item->product->id}}]" id="warehouse_{{$item->product->id}}">
                                                    <option value=""> Select Option</option>
                                                    @if(isset($item->product->relInventoryDetails))
                                                    @foreach($item->product->relInventoryDetails as $data)
                                                    @if(in_array($data->warehouse_id, auth()->user()->relUsersWarehouse->pluck('id')->toArray()))
                                                    @php
                                                        $itemWarehouseStock = \App\Models\PmsModels\InventoryModels\InventoryDetails::where([
                                                            'warehouse_id' => $data->warehouse_id,
                                                            'category_id' => $item->product->category_id,
                                                            'product_id' => $item->product_id,
                                                            'hr_unit_id' => (isset(auth()->user()->employee->as_unit_id) ? auth()->user()->employee->as_unit_id : 0),
                                                        ])->sum('qty');
                                                    @endphp
                                                    <option value="{{$data->warehouse_id}}" data-role="{{$itemWarehouseStock}}"> {!! $data->relWarehouse->name . '('.$itemWarehouseStock.')' !!}</option>
                                                    @endif
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </td>
                                        </tr>
                                        @php
                                        $total_stock_qty += isset($item->product->relInventorySummary->qty)?$item->product->relInventorySummary->qty:0;
                                        $total_requision_qty += $item->qty;
                                        @endphp
                                        @endif
                                        @endforeach
                                        @endif
                                        <tr>
                                            <td colspan="3" class="text-right">Total</td>
                                            <td colspan="">{{$total_stock_qty}}</td>
                                            <td colspan="1">{{$total_requision_qty}}</td>
                                            <td colspan="1"></td>
                                            <td colspan="4" id="totalDeliveryQty"></td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-row">
                                <input type="hidden" name="requisition_id" value="{{$requisition->id}}">
                                <div class="col-12 text-right">
                                    <button type="submit" class="btn btn-success rounded">{{ __('Confirm Delivery') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page-script')
<script>
    (function ($) {
        "use strcit";

        const calculateDeliveryQty = () => {
            $('.deliveryQty').on('keyup', function () {
                let id = $(this).attr('data-id');

                let deliveryQty= parseFloat($('#delivery_qty_'+id).val());
                let maxQty= parseFloat($('#delivery_qty_'+id).attr('max'));
                let mainLeftQty = parseFloat($('#leftQty_'+id).val());

                if (deliveryQty<=maxQty) {
                $('#leftQtyAfterSubTract_'+id).text(mainLeftQty-deliveryQty);

                    if (deliveryQty>0){
                        $('#warehouse_'+id).attr('required',true);
                    }else {
                        $('#warehouse_'+id).attr('required',false);
                    }

                    let totalDeliveryQty=0;
                    $('.deliveryQty').each(function () {
                        if ($(this).val().length!==0){
                            totalDeliveryQty+=parseInt($(this).val())
                        }
                    })
                    $('#totalDeliveryQty').text(totalDeliveryQty);
                }else{
                    setTimeout(notify('Total delivery qty not greater then left qty.', 'warning'),20000)
                    $('#delivery_qty_'+id).val(maxQty);
                }

                return false;
            });
        };

        calculateDeliveryQty();

    })(jQuery);
</script>

@endsection