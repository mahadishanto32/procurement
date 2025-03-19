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
                    <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                        <thead>
                            <tr>
                                <th width="5%">{{__('SL No.')}}</th>
                                <th>{{__('Request Proposal')}}</th>
                                
                                <th>{{__('Supplier')}}</th>
                                <th class="text-center">{{__('Option')}}</th>
                            </tr>
                        </thead>
                        <tbody >
                            @if(count($quotations)>0)
                            @foreach($quotations as $key=> $values)
                            <tr>
                                <td>{{ ($quotations->currentpage()-1) * $quotations->perpage() + $key + 1 }}</td>

                                <td><a href="javascript:void(0)" onclick="openModal({{$values->relRequestProposal->id}})"  class="btn btn-link">{{$values->relRequestProposal->reference_no}}</a></td>


                                <td>
                                    @if($values->relSelfQuotationSupplierByProposalId)
                                    @foreach($values->relSelfQuotationSupplierByProposalId()->where('is_approved','pending')->get() as $supplier)
                                    <button class="btn btn-sm btn-primary">{{$supplier->relSuppliers->name}}</button>
                                    @endforeach
                                    @endif
                                </td>

                                <td class="text-center action">
                                   <a href="{{route('pms.quotation.quotations.cs.compare',$values->request_proposal_id)}}"  title="Compare Process Analysis"  class="btn btn-success"><i class="las la-border-all"></i></a>

                                   <a href="{{route('pms.quotation.quotations.cs.compare.list',$values->request_proposal_id)}}"  title="Compare Process Analysis"  class="btn btn-success"><i class="las la-list"></i></a>

                               </td>
                           </tr>
                           @endforeach
                           @endif
                       </tbody>

                   </table>
                   <div class="p-3">
                    @if(count($quotations)>0)
                    <ul>
                        {{$quotations->links()}}
                    </ul>

                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="modal" id="requestProposalDetailModal">
    <div class="modal-dialog modal-lg" style="width: 80%">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Request Proposal Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body" id="modalContent"></div>
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
    function openModal(requestId) {
        $('#modalContent').empty().load('{{URL::to("pms/rfp/request-proposal")}}/'+requestId);
        $('#requestProposalDetailModal').modal('show')
    }
</script>
@endsection