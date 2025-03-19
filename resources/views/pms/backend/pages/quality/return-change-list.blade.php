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
                <li>
                    <a href="#">PMS</a>
                </li>
                <li class="active">{{__($title)}}</li>
                <li class="top-nav-btn">
                    <a href="{{ route('pms.quality.ensure.return.change.list') }}" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>
                </li>
            </ul>
        </div>

        <div class="page-content">

            <div class="panel">
                <form action="{{route('pms.quality.ensure.return.change.received')}}" method="POST">
                    @csrf
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Sl No.</th>
                                    <th>Category</th>
                                    <th>Product</th>
                                    <th>Unit Price</th>
                                    <th>Gate-In Qty</th>
                                    <th>Previous Received Qty</th>
                                    <th>Return Qty</th>
                                    <th>Receive Qty</th>
                                    {{-- <th>Price</th> --}}
                                </tr>
                            </thead>
                            <tbody>

                                @php
                                $sumOfReceivedtQty=0;
                                $sumOfPrice=0;
                                $sumOfReturnQty=0;
                                @endphp
                                @if(isset($changed))
                                @foreach($changed as $key=>$item)
                                @php 
                                $received_qty = $item->relGoodsReceivedItems->received_qty;
                                $sumOfReceivedtQty +=($received_qty);
                                $returnQty=($item->relGoodsReceivedItems->qty-$received_qty);
                                $sumOfPrice +=($returnQty*$item->relGoodsReceivedItems->unit_amount);
                                $sumOfReturnQty +=$returnQty;

                                @endphp

                                <tr id="removeApprovedRow{{$item->id}}">
                                    <td>{{$key+1}}</td>
                                    <td>{{$item->relGoodsReceivedItems->relProduct->category->name}}</td>
                                    <td>{{$item->relGoodsReceivedItems->relProduct->name}} ({{ getProductAttributes($item->relGoodsReceivedItems->relProduct->id) }})</td>
                                    <td>
                                        <input type="number" readonly class="form-control" id="unitAmount{{$item->id}}" value="{{$item->relGoodsReceivedItems->unit_amount}}">
                                    </td>
                                    <td>{{number_format($item->relGoodsReceivedItems->qty,0)}}</td>
                                    <td>{{$received_qty}}</td>
                                    <td>{{$returnQty}}</td>
                                    <td>
                                        <input type="number" name="received_qty[]" class="form-control" value="{{isset($returnQty)?$returnQty:0}}" min="1" max="{{isset($returnQty)?$returnQty:0}}" onKeyPress="if(this.value.length==4) return false;" 
                                        id="receivedQty{{$item->id}}" {{-- onkeyup="calculateSubtotal({{$item->id}})" --}} >
                                    </td>
                                    {{-- <td>
                                        <span id="sumOfItemPrice{{$item->id}}" class="calculateSumOfSubtotal">
                                            {{$returnQty*$item->relGoodsReceivedItems->unit_amount}}
                                        </span>
                                    </td> --}}

                                    <input type="hidden" name="id[]" class="form-control" value="{{$item->goods_received_item_id}}">
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan="4" class="text-right">Total</td>
                                    <td colspan="">{{isset($changed)?number_format($changed->sum('qty'),0):0}}</td>

                                    <td>{{isset($sumOfReceivedtQty)?number_format($sumOfReceivedtQty,0):0}}</td>
                                    <td>{{isset($sumOfReturnQty)?$sumOfReturnQty:0}}</td>
                                    <td>
                                        <input type="hidden" name="status" class="form-control" value="received">
                                    </td>
                                    {{-- <td id="sumOfPrice">{{isset($sumOfPrice)?number_format($sumOfPrice,2):0}}</td> --}}
                                    
                                </tr>
                                @else
                                <tr>
                                    <td colspan="9" class="text-right">No Data Found</td>
                                </tr>
                                @endif

                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-md-8">
                                <textarea name="return_note" class="form-control" rows="2" placeholder="Notes"></textarea>
                            </div>
                            <div class="col-md-4">
                                <div class="col-12 text-right">
                                    <button type="submit" class="btn btn-primary rounded">{{ __('Return Change Received') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-script')
<script>
  
 function calculateSubtotal(id){

    let unitAmount = $('#unitAmount'+id).val();
    let qty = $('#receivedQty'+id).val();

    if(unitAmount !='' && qty !=''){

        let subTotal = parseFloat(unitAmount*qty).toFixed(2);
        $('#sumOfItemPrice'+id).html(subTotal);

        var total=0;
        $(".calculateSumOfSubtotal").each(function(){
            total += parseFloat($(this).html()||0);
        });

        $("#sumOfPrice").html(parseFloat(total).toFixed(2));

    }else{
        notify('Please enter unit price and qty!!','error');
    }
    return false;
}
</script>
@endsection
