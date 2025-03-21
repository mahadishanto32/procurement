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
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                   <table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="{{ $title }}" border="0">
                    <thead>
                        <tr>
                            <th width="5%">{{__('SL No.')}}</th>
                            <th>{{__('Approved Date')}}</th>
                            <th>{{__('Reference No')}}</th>
                            <th>{{__('Supplier')}}</th>

                            <th>{{__('Quotation Ref No')}}</th>
                            <th>{{__('Total Price')}}</th>
                            <th>{{__('Discount')}}</th>
                            <th>{{__('Vat')}}</th>
                            <th>{{__('Gross Price')}}</th>

                            <th class="text-center">{{__('Option')}}</th>
                        </tr>
                    </thead>
                    <tbody id="viewResult">
                        @if(count($purchaseOrderList)>0)
                        @foreach($purchaseOrderList as $key=> $values)
                        <tr>
                            <td>{{ ($purchaseOrderList->currentpage()-1) * $purchaseOrderList->perpage() + $key + 1 }}</td>
                            <td>{{date('d-m-Y',strtotime($values->po_date))}}</td>
                            <td> <a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="{{route('pms.purchase.order-list.show',$values->id)}}">{{$values->reference_no}}</a></td>

                            <td>{{$values->relQuotation?$values->relQuotation->relSuppliers->name:''}}</td>

                            <td>{{$values->relQuotation?$values->relQuotation->reference_no:''}}</td>
                            <td>{{$values->total_price}}</td>
                            <td>{{$values->discount}}</td>
                            <td>{{$values->vat}}</td>
                            <td>{{$values->gross_price}}</td>


                            <td class="text-center">
                                @if($values->is_send=='no')
                                <a href="{{ route('pms.purchase.send-mail',$values->id) }}" class="btn btn-sm btn-primary"><i class="las la-paper-plane"></i>
                                    {{ __('Mail Send') }}
                                </a>
                                @else
                                {{ __('Already Sent') }}
                                @endif
                                <a target="__blank" href="{{route('pms.billing-audit.po.invoice.print',$values->id)}}" class="btn btn-sm btn-warning"><i class="las la-print"></i>
                                    {{ __('Print View') }}
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
                
                <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right">
                                @if(count($purchaseOrderList)>0)
                                <ul>
                                    {{$purchaseOrderList->links()}}
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
