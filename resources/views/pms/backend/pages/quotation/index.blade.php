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
                    
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="5%">{{__('SL No.')}}</th>
                                    <th>{{__('Request Proposal')}}</th>
                                    <th>{{__('Date')}}</th>
                                    <th>{{__('Reference No')}}</th>
                                    <th>{{__('Supplier')}}</th>
                                    <th>{{__('Total Price')}}</th>
                                    <th>{{__('Discount ')}}</th>
                                    <th>{{__('Vat ')}}</th>
                                    <th>{{__('Gross Price')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Quotation Type')}}</th>
                                    <th class="text-center">{{__('Option')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($proposals)>0)
                                @foreach($proposals as $pkey=> $proposal)
                                    @if($proposal->relQuotations->count() > 0)
                                    @foreach($proposal->relQuotations as $rkey => $values)
                                    <tr>
                                        <td>{{ $rkey+ 1 }}</td>
                                        @if($rkey == 0)
                                        <td rowspan="{{ $proposal->relQuotations->count() }}"><a href="javascript:void(0)" onclick="openRequestProposalModal({{$values->request_proposal_id}})"  class="btn btn-link">{{$values->relRequestProposal->reference_no}}</a></td>
                                        @endif

                                        <td>{{date('d-m-Y',strtotime($values->quotation_date))}}</td>
                                        <td><a href="javascript:void(0)" onclick="openModal({{$values->id}})"  class="btn btn-link">{{$values->reference_no}}</a></td>
                                        <td>{{$values->relSuppliers->name}}</td>
                                        
                                        <td>{{$values->total_price}}</td>
                                        <td>{{$values->discount}} </td>
                                        <td>{{$values->vat}} </td>
                                        <td>{{$values->gross_price}}</td>
                                        <td>{{ucfirst($values->status)}}</td>
                                        <td>{{ucfirst($values->type)}}</td>
                                        
                                        <td class="text-center action">
                                           <a href="javascript:void(0)" onclick="openModal({{$values->id}})"  class="btn btn-info"><i class="las la-eye"></i></a>

                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                @endforeach
                                @endif
                            </tbody>
                           
                        </table>
                        <div class="p-3">
                            @if(count($proposals)>0)
                                <ul>
                                    {{$proposals->links()}}
                                </ul>

                                @endif
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="requisitionDetailModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Quotations Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body" id="tableData">

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

   function openModal(quotation_id) {
    $('#tableData').load('{{URL::to(Request()->route()->getPrefix()."/quotation-items")}}/'+quotation_id);
    $('#requisitionDetailModal').find('.modal-title').html(`Quotation Details`);
    $('#requisitionDetailModal').modal('show');
}

 function openRequestProposalModal(id) {
        $('#tableData').load('{{URL::to('pms/rfp/request-proposal')}}/'+id);
        $('#requisitionDetailModal').find('.modal-title').html(`Proposal Details`);
        $('#requisitionDetailModal').modal('show')
    }
</script>
@endsection