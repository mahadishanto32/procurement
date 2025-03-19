@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
@endsection
@section('main-content')
@php
    use Illuminate\Support\Facades\URL;
    use Illuminate\Support\Facades\Request;
@endphp
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
                    <form  method="post" id="updateInventoryForm" action="{{ route('pms.rfp.store-requistion.purchase') }}">
                        @csrf
                        <div class="panel-body">

                             <div class="row">

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {!! Form::label('request_date', 'Date', array('class' => 'mb-1 font-weight-bold')) !!} 
                                            {!! Form::text('request_date',Request::old('request_date')?Request::old('request_date'):date('d-m-Y'),['id'=>'request_date','class' => 'form-control rounded air-datepicker','placeholder'=>'','readonly'=>'readonly']) !!}

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {!! Form::label('reference_no', 'Reference No', array('class' => 'mb-1 font-weight-bold')) !!}
                                            {!! Form::text('reference_no',$refNo,['id'=>'reference_no','required'=>true,'class' => 'form-control rounded','placeholder'=>'Enter Reference No','readonly']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {!! Form::label('supplier_id', 'Supplier', array('class' => 'mb-1 font-weight-bold')) !!}
                                            {!! Form::Select('supplier_id', $supplierList ,Request::old('supplier_id'),['id'=>'supplier_id','required'=>true,'placeholder'=>'--Select One--','class'=>'form-control rounded select2 select2-tags',]) !!}

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {!! Form::label('supplier_payment_terms_id', 'Select Payment Terms', array('class' => 'mb-1 font-weight-bold')) !!}
                                            <select class="form-control select2" name="supplier_payment_terms_id" id="supplier_payment_terms_id" required>
                                                <option>--Select One--</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                               {{--  <div class="col-md-3 col-sm-12">
                                    <p><label for="QuotationFile" class="font-weight-bold">{{ __('Quotation File (Pdf)') }}:</label></p>
                                    <div class="input-group input-group-md d-">
                                        <input type="file" name="quotation_file" id="QuotationFile" class="form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" value="" accept="application/pdf">
                                    </div>
                                </div> --}}

                                <div class="col-md-2 col-sm-12">
                                    <p><label class="font-weight-bold" for="discount_percent">{{ __('Discount Percent %') }}:</label></p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        {{ Form::number('discount_percent', old('discount_percent')?old('discount_percent'):0, ['class'=>'form-control rounded', 'placeholder'=>'0%','min'=>'0','max'=>'100','step'=>'0.05','id'=>'discount_percent']) }}
                                        <input type="checkbox" name="checkall_discount" id="checkAllDiscount" class="form-control">
                                    </div>
                                    
                                </div>


                            </div><!--end row -->
                            
                                <div class="table-responsive mt-10">
                                <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>Sl No.</th>
                                            <th>Category</th>
                                            <th>Product</th>
                                            <th>Unit</th>
                                            <th>Unit Price</th>
                                            <th>Qty</th>
                                            <th>Item Total</th>
                                            <th>Discount</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if(isset($requisition))
                                        @php 
                                            $sumOfSubtoal=0;
                                            $sumOfVat=0;

                                        @endphp
                                        @foreach($requisition as $key=>$item)
                                        @php 
                                            $subtotal=$item->product->unit_price*$item->qty;
                                            $sumOfSubtoal +=$subtotal;
                                            $vatCalculation= ($item->product->tax * $subtotal)/100;
                                            $sumOfVat +=$vatCalculation
                                        @endphp
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$item->product->category?$item->product->category->name:''}}</td>
                                            <td>
                                                {{$item->product->name}}
                                                <input type="hidden" name="product_id[]" class="form-control" value="{{$item->product->id}}">
                                            </td>
                                            <td>
                                                {{$item->product->productUnit->unit_name}}
                                            </td>
                                            <td>
                                                <input type="number" name="unit_price[{{$item->product->id}}]" required class="form-control" onKeyPress="if(this.value.length==8) return false;"  min="0.0" step="0.01"  id="unit_price_{{$item->product->id}}" placeholder="0.00" onkeyup="calculateSubtotal({{$item->product->id}})" value="{{$item->product->unit_price}}">
                                            </td>

                                            <td>
                                                <input type="number" name="qty[{{$item->product->id}}]" required class="form-control"  min="0" id="qty_{{$item->product->id}}" onKeyPress="if(this.value.length==4) return false;" onkeyup="calculateSubtotal({{$item->product->id}})" step="0.01" value="{{($item->delivery_qty>0)?number_format($item->qty-$item->delivery_qty,0): $item->qty}}">

                                                  <input type="hidden" name="request_qty[{{$item->product->id}}]" min="1" max="99999999" value="{{($item->delivery_qty>0)?number_format($item->qty-$item->delivery_qty,0): $item->qty}}" class="form-control rounded">

                                                <input type="hidden" name="product_vat" id="product_vat_{{$item->product->id}}" value="{{$item->product->tax}}" class="form-control calculateProductVat">
                                            </td>
                                            <td>
                                                <input type="number" name="sub_total_price[{{$item->product->id}}]" required class="form-control calculateSumOfSubtotal" readonly id="sub_total_price_{{$item->product->id}}" placeholder="0.00" min="0.0" step="0.01" value="{{$subtotal}}" >

                                                <input type="hidden" name="sub_total_vat_price[{{$item->product->id}}]" required class="form-control calculateSumOfVat" readonly id="sub_total_vat_price{{$item->product->id}}" placeholder="0.00" value="{{($item->product->tax * $subtotal)/100}}" >
                                            </td>
                                            <td>
                                                <input type="number" name="item_discount_percent[{{$item->product->id}}]" required class="form-control calculateDiscount bg-white" id="item_discount_percent_{{$item->product->id}}" placeholder="0" step="0.01" onKeyPress="if(this.value.length==5) return false;" onkeyup="calculateItemDiscount({{$item->product->id}})" data-id="{{$item->product->id}}">

                                                <input type="hidden" name="item_discount_amount[{{$item->product->id}}]" id="itemWiseDiscount_{{$item->product->id}}" class="itemWiseDiscount" value="{{$item->product->tax}}">
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif

                                        <tr>
                                            <td colspan="6" class="text-right">Sub Total</td>
                                            <td>
                                                <input type="number" name="sum_of_subtoal" readonly class="form-control" id="sumOfSubtoal" placeholder="0.00" value="{{$sumOfSubtoal}}">
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="text-right">(-) Discount</td>
                                            <td>
                                                <input type="text" name="discount" class="form-control bg-white" step="0.01" id="discount" placeholder="0.00" value="0.00">
                                                <input type="hidden" id="sub_total_with_discount" name="sub_total_with_discount"  min="0" placeholder="0.00">
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="text-right">(+) Vat</td>
                                            <td>
                                                <input type="number" step="0.01" onkeyup="vatCalculate()" name="vat" class="form-control bg-white" id="vat" placeholder="0.00" value="{{$sumOfVat}}">
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="text-right">Gross Amount</td>
                                            <td><input type="text" name="gross_price" readonly class="form-control" id="grossPrice" placeholder="0.00" value="{{number_format($sumOfSubtoal+$sumOfVat,2)}}"></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                             <div class="form-row">
                                    <input type="hidden" name="requisition_id" value="{{$req_id}}">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-success rounded">{{ __('Send To Purchase') }}</button>
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
    "use strcit"
    function calculateSubtotal(id) {

        let unit_price = $('#unit_price_'+id).val();
        let qty = $('#qty_'+id).val();

        if(unit_price !='' && qty !=''){

            let sub_total = parseFloat(unit_price*qty).toFixed(2);
            $('#sub_total_price_'+id).val(sub_total);

            var total=0
            $(".calculateSumOfSubtotal").each(function(){
                total += parseFloat($(this).val()||0);
            });
            $("#sumOfSubtoal").val(parseFloat(total).toFixed(2));

            calculateItemDiscount(id);
            //discountCalculate();
            calculateSumOfVat(id);
            
        }else{
            notify('Please enter unit price and qty!!','error');
        }

        return false;
    }

    function discountCalculate() {
        let sumOfSubtoal = parseFloat($('#sumOfSubtoal').val()||0).toFixed(2);
        let discount = parseFloat($('#discount').val()||0).toFixed(2);

        if(sumOfSubtoal !=null && discount !=null){
            
            let grossPrice = parseInt(sumOfSubtoal)-parseInt(discount);

            $('#grossPrice').val(parseFloat(grossPrice).toFixed(2));
            $('#sub_total_with_discount').val(parseFloat(grossPrice).toFixed(2));

        }
        return false;
    }

   

    //calculate discount
    $('#checkAllDiscount:checkbox').on('change', function () {
       let discount_parcent = $('#discount_percent').val();
       //push discount parcentage in all item and set readonly attribute

        if($('#checkAllDiscount:checkbox').prop('checked')){
            $(".calculateDiscount").val(discount_parcent);
            $(".calculateDiscount").attr('readonly',true);

            let discountFields=document.querySelectorAll('.calculateDiscount');
            Array.from(discountFields).map((item, key)=>{
                calculateItemDiscount(item.getAttribute('data-id'));
            })
        }
        else{

            $(".calculateDiscount").val(0);
            $(".calculateDiscount").attr('readonly',false);
            $('#discount_percent').val(0);

            let discountFields=document.querySelectorAll('.calculateDiscount');
            Array.from(discountFields).map((item, key)=>{
                calculateItemDiscount(item.getAttribute('data-id'));
            })
        }

    });

    function calculateItemDiscount(id) {
        let sub_total_price = parseFloat($('#sub_total_price_'+id).val()||0).toFixed(2);
        let item_discount_percent= parseFloat($('#item_discount_percent_'+id).val()||0).toFixed(2);

        if(sub_total_price !='' && item_discount_percent !=''){

            let value = (item_discount_percent * sub_total_price)/100;
            
            $('#itemWiseDiscount_'+id).val(parseFloat(value).toFixed(2));

            //item wise sum
            let total=0;
            $(".itemWiseDiscount").each(function(){
                total += parseFloat($(this).val()||0);
            });

            $("#discount").val(parseFloat(total).toFixed(2));
            discountCalculate();
            vatCalculate();
        }else{
            notify('Please enter unit price and qty!!','error');
        }

        return false;
    }

    $('#discount').on('keyup', function () {

        $(".calculateDiscount").val(0);
        $(".itemWiseDiscount").val(0);
        $(".calculateDiscount").attr('readonly',true);
        $('#discount_percent').val(0);
        discountCalculate();
        vatCalculate();
    });

     function vatCalculate() {

        let price = parseFloat($('#sub_total_with_discount').val()).toFixed(2);
        let vat = parseFloat($('#vat').val()).toFixed(2);
        //let vat = (parcentage * price)/100;
        let total = parseInt(price)+parseInt(vat);
        
        $('#grossPrice').val(parseFloat(total).toFixed(2));
    }

     function calculateSumOfVat(id) {
        let sub_total_price = parseFloat($('#sub_total_price_'+id).val()||0).toFixed(2);
        let product_vat= parseFloat($('#product_vat_'+id).val()||0).toFixed(2);

        if(sub_total_price !='' && product_vat !=''){

            let value = (product_vat * sub_total_price)/100;
            
            $('#sub_total_vat_price'+id).val(parseFloat(value).toFixed(2));

            //item wise sum
            let total=0;
            $(".calculateSumOfVat").each(function(){
                total += parseFloat($(this).val()||0);
            });

            $("#vat").val(parseFloat(total).toFixed(2));

            discountCalculate();
            vatCalculate();
        }else{
            notify('Please enter unit price and qty!!','error');
        }

        return false;
    }

    $('#vat').on('keyup', function () {
        $(".calculateSumOfVat").val(0);
    });

    $('#supplier_id').on('change', function () {
        $.ajax({
            url: "{{ url('pms/rfp/get-supplier-payment-terms') }}/"+$(this).val(),
            type: 'GET',
            data: {},
        })
        .done(function(response) {
            $('#supplier_payment_terms_id').html(response);
        });

    });
</script>
@endsection