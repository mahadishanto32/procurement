@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
<style type="text/css">
    .col-form-label{
        font-size: 14px;
        font-weight: 600;
    }

    .list-unstyled .ratings {
        display: none;
    }
</style>
@endsection
@section('main-content')
@php
use App\Models\PmsModels\Purchase\PurchaseOrderAttachment;
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
                <li class="active">Bill Manage</li>
                <li class="active">{{__($title)}}</li>

            </ul>
        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <div class="panel-body">
                        <form action="{{ route('pms.billing-audit.po.list') }}" method="get" accept-charset="utf-8">
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <p class="mb-1 font-weight-bold"><label for="search_text">Enter Search Text</label></p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <input type="text" name="search_text" id="search_text" class="form-control" placeholder="Search Here..." value="{{ request()->has('search_text') ? request()->get('search_text') : '' }}"/>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <p class="mb-1 font-weight-bold"><label for="from_date">{{ __('From Date') }}:</label></p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <input type="text" name="from_date" id="from_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{  request()->has('from_date')? request()->get('from_date'):date("d-m-Y", strtotime(date('Y-m-01'))) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <p class="mb-1 font-weight-bold"><label for="to_date">{{ __('To Date') }}:</label></p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <input type="text" name="to_date" id="to_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{ request()->has('to_date')? request()->get('to_date'):date('d-m-Y') }}" readonly>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6">
                                            <p class="mb-1 font-weight-bold"><label for=""></label></p>
                                            <div class="input-group input-group-md">
                                                <button type="submit" class="btn btn-success btn-block rounded mt-8"><i class="las la-search"></i>&nbsp;Search</button>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <p class="mb-1 font-weight-bold"><label for=""></label></p>
                                            <div class="input-group input-group-md">
                                                <a href="{{ route('pms.billing-audit.po.list') }}" class="btn btn-danger btn-block rounded mt-8"><i class="las la-times"></i>&nbsp;Clear</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>  
                        </form>                  
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="{{ $title }}" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="5%">{{__('SL')}}</th>
                                    <th>{{__('P.O. Date')}}</th>
                                    <th>{{__('Supplier')}}</th>
                                    <th style="width:10% !important">{{__('Reference No')}}</th>
                                    <th>{{__('P.O Qty')}}</th>
                                    <th class="text-center">{{__('GRN Qty')}}</th>
                                    <th class="text-center">{{__('Po Amount')}}</th>
                                    <th class="text-center">{{__('GRN Amount')}}</th>
                                    <th class="text-center">{{__('Bill Amount')}}</th>
                                    <th class="text-center">{{__('Bill Number')}}</th>
                                    <th class="text-center">{{__('Status')}}</th>
                                    <th class="text-center">{{__('Attachment')}}</th>
                                    <th class="text-center">{{__('Invoice')}}</th>
                                    <th class="text-center">{{__('Vat')}}</th>
                                    <th class="text-center">{{__('Option')}}</th>
                                </tr>
                            </thead>
                            <tbody id="viewResult">
                                @if(count($purchaseOrdersAgainstGrn)>0)
                                @foreach($purchaseOrdersAgainstGrn as $key=> $values)
                                @php
                                $po_attachment=PurchaseOrderAttachment::where(['purchase_order_id'=>$values->id,'bill_type'=>'po'])->first();
                                @endphp
                                @if($values->relGoodsReceivedItemStockIn()->where('is_grn_complete','yes')->sum('total_amount') > 0)
                                <tr>
                                    <td style="width:2% !important">{{ $key + 1 }}</td>
                                    <td style="width:8% !important">{{date('d-m-Y',strtotime($values->po_date))}}</td>
                                    <td>{{$values->relQuotation?$values->relQuotation->relSuppliers->name:''}}</td>
                                    <td> <a href="javascript:void(0)" class="btn-link showPODetails" data-src="{{route('pms.purchase.order-list.show',$values->id)}}">{{$values->reference_no}}</a></td>

                                    <td>{{$values->relPurchaseOrderItems->sum('qty')}}</td>
                                    <td class="text-center">{{$values->total_grn_qty}}</td>
                                    <td class="text-center">{{number_format($values->gross_price,2)}}</td>
                                    <td class="text-center">
                                        @if($values->relGoodsReceivedItemStockIn)

                                        {{number_format($values->relGoodsReceivedItemStockIn()->where('is_grn_complete','yes')->sum('total_amount'),2)}}
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        {{PurchaseOrderAttachment::where('purchase_order_id',$values->id)->sum('bill_amount') > 0 ? number_format(PurchaseOrderAttachment::where('purchase_order_id',$values->id)->sum('bill_amount'), 2)  : 'Not Updated Yet'}}
                                    </td>

                                    <td>{{ isset($po_attachment->bill_number) ? $po_attachment->bill_number : '' }}</td>

                                    <td class="text-center text-left">
                                        @if($values->relPurchaseOrderItems->sum('qty')==$values->total_grn_qty??0)
                                        <button class="btn btn-default">{{__('Full Received')}}</button>
                                        @else
                                        <button class="btn btn-default">{{__('Partial Received')}}</button>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(checkPoAttachment($values->id, "po"))

                                        <a href="javascript:void(0)" class="btn btn-info btn-xs UploadPOAttachment" data-id="{{$values->id}}"><i class="las la-upload"></i>Upload
                                        </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($po_attachment->invoice_file))
                                        <a href="{{asset($po_attachment->invoice_file)}}" target="__blank" class="btn btn-success btn-xs">
                                            <i class="las la-file-invoice"></i>
                                        </a>
                                        @endif
                                    </td>
                                    <td>
                                         @if(!empty($po_attachment->vat_challan_file))
                                        <a href="{{asset($po_attachment->vat_challan_file)}}" class="btn btn-success btn-xs" title="Click here to view vat chalan" target="__blank">
                                            <i class="las la-money-check-alt"></i>
                                        </a>
                                        @endif
                                    </td>
                                    <td class="text-center" style="width:10% !important">
                                        <a href="{{route('pms.billing-audit.po.invoice.list',$values->id)}}" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Click here to view PO Challan" >Chalan({{$values->relGoodReceiveNote->count()}}) </a>

                                        <a target="__blank" href="{{route('pms.billing-audit.po.invoice.print',$values->id)}}" class="btn btn-success btn-xs"><i class="las la-print"></i></a>
                                        @if(!empty($po_attachment->status) &&  !empty($po_attachment->remarks))
                                        <a  po-attachment="{{strip_tags($po_attachment->remarks)}}" class="viewRemarks btn btn-success btn-xs"><i class="las la-eye"></i></a>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                                @endif
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="la-1x pull-right">
                                    @if(count($purchaseOrdersAgainstGrn)>0)
                                    <ul>
                                        {{$purchaseOrdersAgainstGrn->links()}}
                                    </ul>
                                    @endif
                                </div>
                            </div>
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

<div class="modal" id="PurchaseOrderAttachmentModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Purchase Order Attachment</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            {!! Form::open(['route' => 'pms.billing-audit.po.attachment.upload',  'files'=> true, 'id'=>'', 'class' => 'form-horizontal']) !!}

            <div class="modal-body">

            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@endsection
@section('page-script')

<script>
    (function ($) {
        "use script";

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
        };
        showPODetails();

        const UploadPOAttachment = () => {
            $('.UploadPOAttachment').on('click', function () {

                let id = $(this).attr('data-id');

                $.ajax({
                    url: "{{ route('pms.billing-audit.po.list.attachment-upload') }}",
                    type: 'POST',
                    data: {_token: "{{ csrf_token() }}", id:id},
                })
                .done(function(response) {
                    $('#PurchaseOrderAttachmentModal').find('.modal-body').html(response);
                    $('#PurchaseOrderAttachmentModal').modal('show');

                })
                .fail(function(response){
                    notify('Something went wrong!','error');
                });
            });
        };
        UploadPOAttachment();

        const isNumberKey =(evt) => {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
            {
                return false;
            }
            return true;
        };


        const viewRemarks = () => {
            $('.viewRemarks').on('click', function () {

                let model = $(this).attr('po-attachment');

                $('#POdetailsModel').find('#body').html(model);
                $('#POdetailsModel').find('.modal-title').html(`Notes From Audit`);
                $('#POdetailsModel').modal('show');
                
            });
        };
        viewRemarks();


    })(jQuery);

</script>

@endsection