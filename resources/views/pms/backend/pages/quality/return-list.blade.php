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
                    <a href="{{ route('pms.quality.ensure.return.list') }}" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">

                <div class="panel-body">
                    <div class="table-responsive">

                        <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Sl No.</th>
                                    <th>Category</th>
                                    <th>Product</th>
                                    <th>Unit</th>
                                    <th>Unit Price</th>
                                    <th>Qty</th>
                                    <th>Received Qty</th>
                                    <th>Return Qty</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                $sumOfReceivedtQty=0;
                                $sumOfReturntQty=0;
                                $sumOfItemQty=0;
                                $sumOfSubtotal=0;

                                $discountAmount= 0;
                                $vatAmount= 0;

                                @endphp
                                @if(isset($returnList))
                                @foreach($returnList as $key=>$item)
                                @php 
                                $received_qty = $item->relGoodsReceivedItems->relGoodsReceivedItemStockIn->sum('received_qty');
                                $sumOfReceivedtQty += ($received_qty);
                                $sumOfItemQty +=($item->relGoodsReceivedItems->qty);
                                $sumOfReturntQty += ($item->return_qty);
                                $sumOfSubtotal += $item->relGoodsReceivedItems->unit_amount*($item->return_qty);

                                $discountAmount += ($item->discount);
                                $vatAmount +=($item->relGoodsReceivedItems->vat);
                                @endphp

                                <tr id="removeApprovedRow{{$item->id}}">
                                    <td>{{$key+1}}</td>
                                    <td>{{$item->relGoodsReceivedItems->relProduct->category->name}}</td>
                                    <td>{{$item->relGoodsReceivedItems->relProduct->name}} ({{ getProductAttributes($item->relGoodsReceivedItems->relProduct->id) }})</td>
                                    <td>{{$item->relGoodsReceivedItems->relProduct->productUnit->unit_name}}</td>
                                    <td>{{$item->relGoodsReceivedItems->unit_amount}}</td>
                                    <td>{{number_format($item->relGoodsReceivedItems->qty,0)}}</td>
                                    <td>{{$received_qty}}</td>
                                    <td>{{$item->return_qty}}</td>
                                    <td>{{number_format($item->relGoodsReceivedItems->unit_amount*$item->return_qty,2)}}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan="5" class="text-right">Total</td>
                                    <td colspan="">{{isset($sumOfItemQty)?number_format($sumOfItemQty,0):0}}</td>
                                    <td>{{isset($sumOfReceivedtQty)?number_format($sumOfReceivedtQty,0):0}}</td>
                                    <td>{{isset($sumOfReturntQty)?number_format($sumOfReturntQty,0):0}}</td>
                                    <td colspan="">{{isset($returnList)?number_format($sumOfSubtotal,2):0}}</td>
                                </tr>
                                <tr>
                                    <td colspan="8" class="text-right">(-) Discount</td>
                                    <td><?= number_format($discountAmount,2)?></td>
                                </tr>
                                <tr>
                                    <td colspan="8" class="text-right">(+) Vat</td>
                                    <td>{{number_format($vatAmount,2)}}</td>
                                </tr>
                                <tr>
                                    <td colspan="8" class="text-right"><strong>Total Amount</strong></td>
                                    <td><strong>{{number_format(($sumOfSubtotal-$discountAmount)+$vatAmount,2)}}</strong></td>
                                </tr>
                                @else
                                <tr>
                                    <td colspan="9" class="text-right">No Data Found</td>
                                </tr>
                                @endif

                            </tbody>
                        </table>
                    </div>
                    
                </div>

            </div>
        </div>
    </div>
</div>



@endsection
@section('page-script')
<script>

</script>
@endsection