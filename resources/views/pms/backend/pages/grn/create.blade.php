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
                    <a href="{{ route('pms.grn.po.list') }}" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="panel p-20">
                <form  method="post" action="{{ route('pms.grn.grn-process.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="panel-body">
                        <div class="row">

                            <div class="col-md-2 col-sm-12">
                                <p class="mb-1 font-weight-bold"><label for="received_date">{{ __(' Date') }}:</label></p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="received_date" id="received_date" class="form-control rounded air-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm"  required readonly value="{{ old('received_date')?old('received_date'):date('d-m-Y') }}">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-12">
                                <p class="mb-1 font-weight-bold"><label for="reference_no">{{ __('Reference No') }}:</label></p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="reference_no" id="reference_no" class="form-control rounded" readonly aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{ ($refNo)?($refNo):0 }}">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-12">
                                <p class="mb-1 font-weight-bold"><label for="supplier_id">{{ __('Supplier') }}:</label></p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" readonly class="form-control rounded" value="{{$purchaseOrder->relQuotation->relSuppliers->name}}">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-12">
                                <p class="mb-1 font-weight-bold"><label for="challan">{{ __('Challan No.') }}:</label></p>
                                <div class="input-group input-group-md mb-3 d-">
                                    {{ Form::text('challan', '', ['class'=>'form-control', 'placeholder'=>'Enter Challan Number here','id'=>'challan','required'=>'required']) }}

                                </div>
                            </div>

                            <div class="col-md-2 col-sm-12">
                                <p class="mb-1 font-weight-bold"><label for="challanFile">{{ __('Challan Photo') }} *:</label></p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="file" name="challan_file" id="challanFile" class="form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" value="" accept="image/*">
                                    <input type="hidden" name="purchase_order_id" value="{{$purchaseOrder->id}}">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive mt-10">
                            <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Category</th>
                                        <th>Product</th>
                                        <th>Unit Price</th>
                                        <th>Po Qty</th>
                                        <th>Prv.Rcv Qty</th>
                                        <th>Receiving Qty</th>
                                        <th>Left Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php 
                                    $totalReceiveQty=0;
                                    $totalPrice=0;
                                    $discount_amount=0;
                                    $total_discount_amount=0;
                                    $vat_amount=0;
                                    $total_vat_amount=0;
                                    @endphp
                                    @if(isset($purchaseOrder->relPurchaseOrderItems))
                                    @foreach($purchaseOrder->relPurchaseOrderItems as $key=>$item)
                                    @php
                                    
                                    $leftQty=isset($item->grn_qty)?$item->grn_qty:$item->qty;
                                    $receiveQty=0;
                                    
                                    if ($item->relReceiveProduct->count() > 0){
                                        $receiveQty =  isset($item->grn_qty) && $item->grn_qty > 0  ? $item->grn_qty : 0;
                                        $totalReceiveQty += $receiveQty;
                                    }
                                    $leftQty=$item->qty-$receiveQty;
                                    $unit_amount=$leftQty*$item->unit_price;
                                    $totalPrice +=$unit_amount;

                                    $discount_amount=($item->discount_percentage*$unit_amount)/100;
                                    $total_discount_amount +=$discount_amount;

                                    $vat_amount=($item->relProduct->tax*$unit_amount)/100;
                                    $total_vat_amount +=$vat_amount;
                                    @endphp
                                    @if($leftQty !=0)
                                    <tr class="grnItemContent">
                                        <td>{{$key+1}}</td>
                                        <td>{{$item->relProduct->category?$item->relProduct->category->name:''}}</td>
                                        <td>
                                            {{$item->relProduct->name}}
                                            <input type="hidden" name="product_id[]" class="form-control" value="{{$item->relProduct->id}}">
                                        </td>

                                        <td>
                                            <input type="text" name="unit_price[{{$item->relProduct->id}}]" class="form-control"   min="0.0"  id="unit_price_{{$item->relProduct->id}}" value="{{$item->unit_price}}" readonly placeholder="0">
                                        </td>
                                        <td>
                                            {{$item->qty}}
                                            <input type="hidden" value="{{$leftQty}}" id="main_qty_{{$item->relProduct->id}}">
                                        </td>
                                        <td id="pre_rcv_qty_{{$item->relProduct->id}}">
                                            {{$receiveQty}}
                                        </td>
                                        <td>
                                            <input type="number" name="qty[{{$item->relProduct->id}}]"  class="form-control bg-white rcvQty"  min="0"  max="{{$leftQty}}" id="receive_qty_{{$item->relProduct->id}}" data-id="{{$item->relProduct->id}}" value="{{$leftQty}}" placeholder="0">
                                        </td>

                                        <td id="left_qty_{{$item->relProduct->id}}"></td>


                                        <input type="hidden" name="unit_amount[{{$item->relProduct->id}}]" value="{{round($unit_amount,2)}}" required readonly class="form-control calculateSumOfSubtotal" id="sub_total_price_{{$item->relProduct->id}}" placeholder="0">
                                        
                                        <input type="hidden" name="discount_percentage[{{$item->relProduct->id}}]" class="form-control readonly rounded discountPercentage" id="discount_percentage_{{$item->relProduct->id}}" readonly value="{{$item->discount_percentage}}">

                                        <input type="hidden" name="item_discount_amount[{{$item->relProduct->id}}]" id="item_wise_discount_{{$item->relProduct->id}}" class="itemWiseDiscount" value="{{$discount_amount}}">
                                        
                                        <input type="hidden" name="vat_percentage[{{$item->relProduct->id}}]" id="vat_percentage_{{$item->relProduct->id}}" data-id="{{$item->relProduct->id}}" value="{{$item->relProduct->tax}}" class="form-control calculateProductVat" readonly>

                                        <input type="hidden" name="sub_total_vat_price[{{$item->relProduct->id}}]" required class="form-control calculateSumOfVat" readonly id="sub_total_vat_price{{$item->relProduct->id}}" placeholder="0.00" value="{{$vat_amount}}">
                                    </tr>
                                    @endif
                                    @endforeach
                                    @endif

                                </tbody>
                            </table>

                        </div>

                        <div class="row">

                            <div class="col-md-12">
                                <p class="mb-1 font-weight-bold"><label for="note">{{ __('Notes.') }}:</label></p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <textarea name="note" class="form-control" rows="2" placeholder="Write Here...."></textarea>
                                </div>
                            </div>
                            
                            <input type="hidden" value="{{round($totalPrice,2)}}" name="total_price" required readonly class="form-control" id="sumOfSubtoal" placeholder="0.00" step="0.0">

                            <input type="hidden" name="discount" class="form-control bg-white" step="0.25" id="discount" readonly placeholder="0.00" value="{{$total_discount_amount}}">

                            <input type="hidden" id="sub_total_with_discount" name="sub_total_with_discount"  min="0" placeholder="0.00">

                            <input type="hidden" name="vat" class="form-control bg-white" id="vat" readonly placeholder="0.00" value="{{$total_vat_amount}}">

                            <input type="hidden" value="{{$gross_price=($totalPrice-$total_discount_amount)+$total_vat_amount}}" name="gross_price" readonly required class="form-control" id="grossPrice" placeholder="0.00">

                            <div class="col-md-12">
                                <div class="mb-3 text-right">
                                    <button type="submit" class="btn btn-primary rounded" style="float:right">{{ __('Receive Product') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="purchase_order_id" value="{{$purchaseOrder->id}}">
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@section('page-script')
<script>
    "use strcit"

    const validateReceiveQty = (item, key) => {
        let UnitPrice = item.querySelectorAll('td')[3];
        let PoQty = item.querySelectorAll('td')[4];
        let PrvRcvQty = item.querySelectorAll('td')[5];
        let ReceivingQty = item.querySelectorAll('td')[6];
        let LeftQty = item.querySelectorAll('td')[7];

        
        let SubPrice = item.querySelector('.calculateSumOfSubtotal');
        // let SubPrice = item.querySelectorAll('td')[8];
        let DiscountPercentage = item.querySelector('.itemWiseDiscount');
        // let DiscountPercentage = item.querySelectorAll('td')[9];
        let VatPercentage = item.querySelector('.calculateProductVat');
        // let VatPercentage = item.querySelectorAll('td')[9];

        ReceivingQty.onkeyup = function (){
            if(PrvRcvQty.innerText.trim() == 0){
                ReceivingQty.querySelector('input').value>parseInt(PoQty.innerText.trim()) ? ReceivingQty.querySelector('input').value=parseInt(PoQty.innerText.trim()): (ReceivingQty.querySelector('input').value === 0 ? ReceivingQty.querySelector('input').value=0:ReceivingQty.querySelector('input').value);
            } else {
                ReceivingQty.querySelector('input').value>(parseInt(PoQty.innerText.trim()) - parseInt(PrvRcvQty.innerText.trim())) ? ReceivingQty.querySelector('input').value=(parseInt(PoQty.innerText.trim()) - parseInt(PrvRcvQty.innerText.trim())): (ReceivingQty.querySelector('input').value<0 ? ReceivingQty.querySelector('input').value=0:ReceivingQty.querySelector('input').value);
            }
            console.log()
            LeftQty.innerText = (parseInt(PoQty.innerText.trim()) - parseInt(PrvRcvQty.innerText.trim()) - ReceivingQty.querySelector('input').value);

        
            SubPrice.value= parseFloat(ReceivingQty.querySelector('input').value).toFixed(2)* parseFloat(UnitPrice.querySelector('input').value).toFixed(2);

            // For Discount Calculation
            let ProductId=ReceivingQty.querySelector('input').getAttribute('data-id');
            let DiscountPercentageValue=DiscountPercentage.value;
            let ItemWiseSubPrice=SubPrice.value;

            let DiscountAmount = (DiscountPercentageValue * ItemWiseSubPrice)/100;
            $('#item_wise_discount_'+ProductId).val(parseFloat(DiscountAmount).toFixed(2));
            //Item wise sum
            let TotalDiscountAmount=0;
            $(".itemWiseDiscount").each(function(){
                TotalDiscountAmount += parseFloat($(this).val()||0);
            });
            $("#discount").val(parseFloat(TotalDiscountAmount).toFixed(2));
            //End Discount Calculation

            let TotalSubTotal=0;
            $('.calculateSumOfSubtotal').each(function(){
                TotalSubTotal += parseFloat($(this).val()||0);
            });

            $('#sumOfSubtoal').val(parseFloat(TotalSubTotal).toFixed(2));
            DiscountCalculate();
            CalculateSumOfVat(ProductId);
        }

    }

    const getAllContent = () => {
        let contents = document.querySelectorAll('.grnItemContent');
        Array.from(contents).map((item, key) => {
            validateReceiveQty(item, key);
        })
    }
    getAllContent();

    const DiscountCalculate=()=>{
        let sumOfSubtoal = parseFloat($('#sumOfSubtoal').val()||0).toFixed(2);
        let discount = parseFloat($('#discount').val()||0).toFixed(2);
        if(sumOfSubtoal !=null && discount !=null){
            let grossPrice = parseInt(sumOfSubtoal)-parseInt(discount);
            $('#grossPrice').val(parseFloat(grossPrice).toFixed(2));
            $('#sub_total_with_discount').val(parseFloat(grossPrice).toFixed(2));
        }
        return false;
    };
    DiscountCalculate();

    function CalculateSumOfVat(id) {
        let sub_total_price = parseFloat($('#sub_total_price_'+id).val()||0).toFixed(2);
        let vat_percentage= parseFloat($('#vat_percentage_'+id).val()||0).toFixed(2);

        if(sub_total_price !='' && vat_percentage !=''){
            let value = (vat_percentage * sub_total_price)/100;
            $('#sub_total_vat_price'+id).val(parseFloat(value).toFixed(2));
            let total=0;
            $(".calculateSumOfVat").each(function(){
                total += parseFloat($(this).val()||0);
            });

            $("#vat").val(parseFloat(total).toFixed(2));

            DiscountCalculate();
            VatCalculate();
        }else{
            notify('Please enter unit price and qty!!','error');
        }

        return false;
    }

    const VatCalculate=()=> {
        let price = parseFloat($('#sub_total_with_discount').val()).toFixed(2);
        let vat = parseFloat($('#vat').val()).toFixed(2);
        let total = parseInt(price)+parseInt(vat);
        $('#grossPrice').val(parseFloat(total).toFixed(2));
    }
    VatCalculate();

</script>
@endsection