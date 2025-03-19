<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>Quotation Submit - MBM ERP</title>
<!-- Favicon -->
<link rel="shortcut icon" href="{{ asset('images/mbm.ico')}} " />


<!-- Styles -->
<link href="{{ asset('css/app.css') }}" rel="stylesheet" media='screen,print'>
<link rel="stylesheet" href="{{ asset('assets/css/all.css') }}" media='screen,print'>
@stack('css')
{{-- Custom CSS --}}
<link rel="stylesheet" href="{{ asset('assets/css/custom.css?v=1.3') }}" media='screen,print'>
<!-- Responsive CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}" media='screen,print'>
<!-- jQuery Confirm -->
<link rel="stylesheet" href="{{asset('assets/js/jquery-confirm/jquery-confirm.min.css')}}" />
<!-- toastr alert -->
<link rel="stylesheet" href="{{asset('notification_assets/css/toastr.min.css')}}" />
<link rel="stylesheet" href="{{asset('plugins/air-datepicker/css/datepicker.min.css')}}" />
<link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">

@yield('page-css')
</head>
<body>
@include('pms.backend.layouts.pre-loader')
<div id="app">
<div class="wrapper">
<div id="content-page" class=" container">
<main class="" style="padding-bottom: 0;">
<div id="main-body" class="">
<div class="main-content">
<div class="main-content-inner">
<div class="breadcrumbs ace-save-state" id="breadcrumbs">
<ul class="breadcrumb" style="background-color: #07a1b2;">
<li>
<i class="ace-icon fa fa-home home-icon"></i>
<a href="javascript:void(0)">
    <img src="{{ asset('images/mbm-logo-w.png') }}" class="img-fluid" alt="MBM" style="height: 35px;">

</a>
</li>
<li class="active">
PMS
</li>
<li class="active">{{__('Quotation')}}</li>
<li class="active">{{__($title)}}</li>

</ul>
</div>

<div class="page-content">
<div class="">
<div class="panel panel-info" style="border-radius: 15px 15px 0px 0px;margin-bottom: 0px;border-bottom: 1px solid #c4bcbc;">
<form  method="post" action="{{ route('pms.rfp.online.quotations.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="panel-body">
        <div class="row">
            <div class="col-md-2 col-sm-12">
                <p class="mb-1 font-weight-bold"><label for="quotation_date">{{ __(' Date') }}:</label></p>
                <div class="input-group input-group-md mb-3 d-">
                    <input type="text" name="quotation_date" id="quotation_date" class="form-control rounded air-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm"  required readonly value="{{ old('quotation_date')?old('quotation_date'):date('d-m-Y') }}">
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
                    <input type="text"   class="form-control rounded" readonly aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{$supplier->name}}">
                    <input type="hidden" value="{{$supplier->id}}" name="supplier_id" required readonly>

                </div>
            </div>

            <div class="col-md-3 col-sm-12">
                <p class="mb-1 font-weight-bold"><label for="QuotationFile">{{ __('Quotation File (Pdf)') }} *:</label></p>
                <div class="input-group input-group-md mb-3 d-">
                    <input type="file" name="quotation_file" id="QuotationFile" class="form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" value="" accept="application/pdf">
                </div>
            </div>
            <div class="col-md-2 col-sm-12">
                <p class="mb-1 font-weight-bold"><label for="discount_percent">{{ __('Discount Percent %') }}:</label></p>
                <div class="input-group input-group-md mb-3 d-">

                    <input type="number" name="discount_percent" id="discount_percent" class="form-control rounded" min="0" max="100" step="0.5">

                    <input type="checkbox" name="checkall_discount" id="checkAllDiscount" class="form-control">
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
                        <th>Unit</th>
                        <th>Unit Price</th>
                        <th>Qty</th>
                        <th>Item Total</th>
                        <th>Discount</th>
                    </tr>
                </thead>
                <tbody>

                    @if(isset($requestProposal->requestProposalDetails))

                    @foreach($requestProposal->requestProposalDetails as $key=>$item)
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
                            <input type="number" name="unit_price[{{$item->product->id}}]" required class="form-control" onKeyPress="if(this.value.length==8) return false;"  min="0.0" step="0.01"  id="unit_price_{{$item->product->id}}" placeholder="0.00" onkeyup="calculateSubtotal({{$item->product->id}})">
                        </td>

                        <td>
                            <input type="number" name="qty[{{$item->product->id}}]" required class="form-control"  min="0" id="qty_{{$item->product->id}}" value="{{round($item->request_qty)}}" onKeyPress="if(this.value.length==4) return false;" onkeyup="calculateSubtotal({{$item->product->id}})" step="0.01">

                            <input type="hidden" name="product_vat" id="product_vat_{{$item->product->id}}" value="{{$item->product->tax}}" class="form-control calculateProductVat">
                        </td>
                        <td>
                            <input type="number" name="sub_total_price[{{$item->product->id}}]" required class="form-control calculateSumOfSubtotal" readonly id="sub_total_price_{{$item->product->id}}" placeholder="0.00" value="0.00" >

                            <input type="hidden" name="sub_total_vat_price[{{$item->product->id}}]" required class="form-control calculateSumOfVat" readonly id="sub_total_vat_price{{$item->product->id}}" placeholder="0.00" value="0.00" >
                        </td>
                        <td>
                            <input type="number" name="item_discount_percent[{{$item->product->id}}]" required class="form-control calculateDiscount bg-white" id="item_discount_percent_{{$item->product->id}}" placeholder="0" step="0.01" onKeyPress="if(this.value.length==5) return false;" onkeyup="calculateItemDiscount({{$item->product->id}})" data-id="{{$item->product->id}}">

                            <input type="hidden" name="item_discount_amount[{{$item->product->id}}]" id="itemWiseDiscount_{{$item->product->id}}" class="itemWiseDiscount">
                        </td>
                    </tr>
                    @endforeach
                    @endif

                    <tr>
                        <td colspan="6" class="text-right">Sub Total</td>
                        <td>
                            <input type="number" name="sum_of_subtoal" readonly class="form-control" id="sumOfSubtoal" placeholder="0.00">
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
                        <td colspan="6" class="text-right">(+) Vat </td>
                        <td>
                            <input type="number" step="0.01" onkeyup="vatCalculate()" name="vat" class="form-control bg-white" id="vat" placeholder="0.00" value="0.00">
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-right">Gross Amount</td>
                        <td><input type="number" name="gross_price" readonly class="form-control" id="grossPrice" placeholder="0.00"></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="form-row">

            <input type="hidden" name="request_proposal_id" value="{{$requestProposal->id}}">
            <input type="hidden" name="type" value="online">

            @if(count($submitQuotation)<1)
            <div class="col-12 text-right">
                <button type="submit" class="btn btn-primary rounded">{{ __('Submit Your Quotation') }}</button>
            </div>
            @endif
        </div>
    </div>

</form>
</div>
</div>
</div>
</div>
</div>
</div>
</main>
<!------------------------------------------------------------------------------------------------>
<footer class="bg-white iq-footers mr-0" style="position: static;padding: 15px;border-radius: 0 0 15px 15px;">
<div class="container">
<div class="row">
<div class="col-lg-6">
<ul class="list-inline mb-0">
<li class="list-inline-item"><a href="javascript:void(0)">Privacy Policy</a></li>
<li class="list-inline-item"><a href="javascript:void(0)">Terms of Use</a></li>
</ul>
</div>
<div class="col-lg-6 text-right">
Copyright 2018 - {{date('Y')}} <a>MBM Group</a> All Rights Reserved.
</div>
</div>
</div>
</footer>
</div>
<div class="app-loader">
<i class="fa fa-spinner fa-spin"></i>
</div>
</div>
</div>


<script src="{{ asset('js/app.js') }}"></script>
<script src="{{asset('assets/js/all.js')}}"></script>
<!-- jQuery Confirm alert -->
<script src="{{asset('assets/js/jquery-confirm/jquery-confirm.min.js')}}"></script>
<!-- toastr alert -->
<script src="{{asset('notification_assets/js/toastr.min.js')}}"></script>
<!-- sweet alert -->
<script src="{{asset('notification_assets/js/sweetalert.min.js')}}"></script>

<script>
    var count = 0;
    var refreshIntervalId =setInterval(function(){
        count++;
        jQuery(document).ready(function() {
            clearInterval(refreshIntervalId);
            jQuery("#load").fadeOut();
            jQuery("#loading").fadeOut("");

        });
        if( count == 5){
            clearInterval(refreshIntervalId);
            jQuery("#load").fadeOut();
            jQuery("#loading").fadeOut("");
        }
    }, 300);
</script>
<script>
    var loaderContent = '<div class="animationLoading"><div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div></div>';
    let afterLoader = '<div class="loading-select left"><img src="{{ asset('images/loader.gif')}}" /></div>';
</script>
<!-- Custom JavaScript -->
@stack('js')
@toastr_render
<script>
//

$( document ).ajaxComplete(function() {
// Required for Bootstrap tooltips in DataTables
$('[data-toggle="tooltip"]').tooltip({
    "html": true,
//"delay": {"show": 1000, "hide": 0},
});
$('[data-toggle="popover"]').popover({
    html: true,
});
});
$('[data-toggle="popover"]').click(function () {
    $(this).popover('show');
});


$(document).ajaxError(function(event, jqxhr, settings, exception) {
    if (exception == 'Unauthorized') {
        $.notify("Your session has expired!", 'error');
        setTimeout(function(){
            window.location = '{{ url()->full() }}';
        }, 1000)
    }
});
let panelOptions = [];
let Scrollbar = window.Scrollbar;
if (jQuery('.col-panel-scroll').length) {
    Scrollbar.init(document.querySelector('.col-panel-scroll'), panelOptions);
}
let Scrollbar1 = window.Scrollbar;
if (jQuery('.col-panel-scroll1').length) {
    Scrollbar1.init(document.querySelector('.col-panel-scroll1'), panelOptions);
}
let Scrollbar2 = window.Scrollbar;
if (jQuery('.col-panel-scroll2').length) {
    Scrollbar2.init(document.querySelector('.col-panel-scroll2'), panelOptions);
}
// on first focus (bubbles up to document), open the menu
$(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
    $(this).closest(".select2-container").siblings('select:enabled').select2('open');
});
// steal focus during close - only capture once and stop propogation
$('select.select2').on('select2:closing', function (e) {
    $(e.target).data("select2").$selection.one('focus focusin', function (e) {
        e.stopPropagation();
    });
});

//Notify using swal
function notify(message,type) {
    swal({
        icon: type,
        text: message,
        button: false
    });
    setTimeout(()=>{
        swal.close();
    }, 1500);
}
//select 2
$(document).ready(function() {
    $('.select2').select2();

    $(".select2-tags").select2({
        tags: true
    });
});
</script>


<script src="{{ asset('assets/js/custom.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>



<!-- Datetime picker -->
<script src="{{asset('plugins/air-datepicker/js/datepicker.min.js')}}"></script>

<script type="text/javascript">
    $('.air-datepicker').datepicker({
        language: 'en',
        dateFormat: 'dd-mm-yyyy',
        autoClose: true,
    });
</script>


<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>

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
</script>

@include('pms.backend.layouts.toster-script')
</body>

</html>
