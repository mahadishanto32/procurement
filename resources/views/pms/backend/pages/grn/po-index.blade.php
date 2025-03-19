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
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <form action="{{ url('pms/grn/po-list') }}" method="get" accept-charset="utf-8">
                        <div class="row pl-4 pt-3">
                            <div class="col-md-3 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="from_date">{{ __('From Date') }}:</label></p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="from_date" id="from_date" class="search-datepicker form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required readonly value="{{ request()->has('from_date') ? date("d-m-Y", strtotime(request()->get('from_date'))) : date("d-m-Y", strtotime(date('Y-m-01'))) }}">
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="to_date">{{ __('To Date') }}:</label></p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="to_date" id="to_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required readonly value="{{ request()->has('to_date') ? date("d-m-Y", strtotime(request()->get('to_date'))) : date("d-m-Y", strtotime(date('Y-m-d'))) }}">
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="searchPOList"></label></p>
                                <div class="input-group input-group-md">
                                    <button type="submit" class="btn btn-success rounded mt-8"><i class="las la-search"></i>Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="panel panel-body">

                    <div class="table-responsive" id="viewResult">
                        <table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="{{ $title }}" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="5%">{{__('SL No.')}}</th>
                                    <th>{{__('Unit')}}</th>
                                    <th>{{__('PO.Date')}}</th>
                                    <th>{{__('Supplier')}}</th>
                                    <th>{{__('Reference No')}}</th>
                                    <th>{{__('Quotation Ref No')}}</th>
                                    <th>{{__('P.O Qty')}}</th>
                                    <th>{{__('Gate-In Qty')}}</th>
                                    <th>{{__('Total Price')}}</th>
                                    <th>{{__('Discount')}}</th>
                                    <th>{{__('Vat')}}</th>
                                    <th>{{__('Gross Price')}}</th>
                                    <th>{{__('BarCode')}}</th>
                                    <th class="text-center" style="width: 20%">{{__('Option')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($data)>0)
                                @foreach($data as $key=> $values)
                                <tr>
                                    <td>{{ ($data->currentpage()-1) * $data->perpage() + $key + 1 }}</td>
                                    <td>{{$values->Unit->hr_unit_short_name}}</td>
                                    <td>{{date('d-m-Y',strtotime($values->po_date))}}</td>
                                    <td>{{$values->relQuotation?$values->relQuotation->relSuppliers->name:''}}</td>
                                    <td> <a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="{{route('pms.purchase.order-list.show',$values->id)}}">{{$values->reference_no}}</a></td>
                                    <td>{{$values->relQuotation?$values->relQuotation->reference_no:''}}</td>

                                    <td>{{$values->relPurchaseOrderItems->sum('qty')}}</td>
                                    <td>{{$values->total_grn_qty}}</td>

                                    <td>{{$values->total_price}}</td>
                                    <td>{{$values->discount}}</td>
                                    <td>{{$values->vat}}</td>
                                    <td>{{$values->gross_price}}</td>
                                    <td>    
                                    
                                    
                                    <img src="data:image/png;base64,{!!DNS1D::getBarcodePNG($values->reference_no, 'C39',1,33) !!}" alt="barcode" />
                                    </td>

                                    <td class="text-center">
                                        @if($values->relPurchaseOrderItems->sum('qty')==$values->total_grn_qty)
                                            <a class="btn btn-success btn-xs mb-1">{{__('Full Received')}}</a>
                                        @elseif($values->relPurchaseOrderItems->sum('qty')>$values->total_grn_qty)

                                            @if($values->total_grn_qty > 0)
                                            <a class="btn btn-warning btn-xs mb-1">{{__('Partially Received')}}</a>
                                            <br>
                                            @endif
                                            
                                            <a href="{{ route('pms.grn.grn-list.createGRN',$values->id) }}" data-toggle="tooltip" title="Click here to generate Gate-In">
                                                <button type="button" class="btn btn-xs btn-primary mb-1">{{ __('Gate-In') }}</button>
                                            </a>
                                        @else
                                            <a href="{{ route('pms.grn.grn-list.createGRN',$values->id) }}" data-toggle="tooltip" title="Click here to generate Gate-In">
                                                <button type="button" class="btn btn-sm btn-primary">{{ __('Gate-In') }}</button>
                                            </a>
                                        @endif

                                        @if($values->total_grn_qty > 0)
                                        <br>
                                        <a class="btn btn-primary btn-xs" href="{{ url('pms/grn/gate-in-slip/'.$values->id) }}" target="_blank"><i class="la la-print"></i></a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                        <div class="col-12 py-2">
                            @if(count($data)>0)
                            <ul>
                                {{$data->links()}}
                            </ul>

                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="POdetailsModel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Purchase Order</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body" id="body">

            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

@endsection
@section('page-script')

<script>
    (function ($) {
        "use script";

        $('#searchPOList').on('click', function () {

            let from_date=$('#from_date').val();
            let to_date=$('#to_date').val();

            const searchPOList = () => {
                let container = document.querySelector('.searchPagination');
                let pageLink = container.querySelectorAll('.page-link');
                Array.from(pageLink).map((item, key) => {
                    item.addEventListener('click', (e)=>{
                        e.preventDefault();
                        let getHref = item.getAttribute('href');
                        showPreloader('block');
                        $.ajax({
                            type: 'post',
                            url: getHref,
                            dataType: "json",
                            data:{_token:'{!! csrf_token() !!}',from_date:from_date,to_date:to_date},
                            success:function (data) {
                                if(data.result == 'success'){
                                    showPreloader('none');
                                    $('#viewResult').html(data.body);
                                    searchPOList();
                                     showPODetails();
                                }else{
                                    showPreloader('none');
                                    notify(data.message,'error');

                                }
                            }
                        });
                    })

                });
            };
            
            if (from_date !='' || to_date !='') {
                showPreloader('block');
                $.ajax({
                    type: 'post',
                    url: $(this).attr('data-src'),
                    dataType: "json",
                    data:{_token:'{!! csrf_token() !!}',from_date:from_date,to_date:to_date},
                    success:function (data) {
                        if(data.result == 'success'){
                            showPreloader('none');
                            $('#viewResult').html(data.body);
                            searchPOList();
                             showPODetails();
                        }else{
                            showPreloader('none');
                            $('#viewResult').html('<div class="col-md-12"><center>No Data Found!!</center></div>');

                        }
                    }
                });
                return false;
            }else{
                notify('Please enter data first !!','error');
            }
        });


        const showPODetails = () => {
            $('.showPODetails').on('click', function () {

                $.ajax({
                    url: $(this).attr('data-src'),
                    type: 'get',
                    dataType: 'json',
                    data: '',
                })
                .done(function(response) {

                    if (response.result=='success') {
                        $('#POdetailsModel').find('#body').html(response.body);
                        $('#POdetailsModel').find('.modal-title').html(`Purchase Order Details`);
                        $('#POdetailsModel').modal('show');
                    }

                })
                .fail(function(response){
                    notify('Something went wrong!','error');
                });
            });
        }
        showPODetails();

    })(jQuery);
</script>

@endsection