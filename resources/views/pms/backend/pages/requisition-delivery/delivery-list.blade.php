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
          <div class="panel-body">

             <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                <thead>
                    <tr>
                       <th width="5%">{{__('SL No.')}}</th>
                       <th>{{__('Req.Date')}}</th>
                       <th>{{__('Requisition By')}}</th>
                       <th>{{__('Delivery Date')}}</th>
                       <th>{{__('Delivery Ref.')}}</th>
                       <th>{{__('Delivery Qty')}}</th>
                       <th>{{__('Delivery By')}}</th>
                   </tr>
               </thead>
               <tbody id="viewResult">

                   @forelse($requisitionDeliveries as $key=> $value)
                   <tr id="row{{$value->id}}">
                     <td>{{$key+1}}</td>
                     <td>{{date('d-m-Y', strtotime($value->relRequisition->requisition_date))}}</td>

                     <td>
                        <a href="javascript:void(0)" onclick="openModal({{$value->relRequisition->id}})"  class="btn btn-link">
                            {{$value->relRequisition->relUsersList->name}}  ({{$value->relRequisition->requisitionItems->sum('qty')}} Qty)
                        </a>
                    </td>
                    <td>{{date('d-m-Y', strtotime($value->delivery_date))}} </td>
                    <td>
                        <a href="javascript:void(0)" onclick="openDeliveryModal({{$value->id}})" ta-toggle="tooltip" title="Click here to view details"  class="btn btn-link">
                            {{$value->reference_no}}
                        </a>
                    </td>
                    <td> {{$value->relDeliveryItems->sum('delivery_qty')}}</td>
                    <td> {{$value->relDeliveryBy->name}}</td>
                </tr>
                @empty
                @endforelse
            </tbody>
            <tfoot>
               <ul>{{$requisitionDeliveries->links()}}</ul>
           </tfoot>
       </table>
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
           <h4 class="modal-title">Requisition Details</h4>
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


<div class="modal" id="deliveryDetailModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Delivery Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body" id="detailTable">

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


function openModal(requisitionId) {
    $('#tableData').load('{{URL::to(Request()->route()->getPrefix()."/store-inventory-compare")}}/'+requisitionId);
    $('#requisitionDetailModal').modal('show');
}

function openDeliveryModal(requisitionDeliveryId) {
    $('#detailTable').load('{{URL::to(Request()->route()->getPrefix()."/requisition-delivered-detail")}}/'+requisitionDeliveryId);
    $('#deliveryDetailModal .modal-title').html(`Delivery Product with Qty`);
    $('#deliveryDetailModal').modal('show');
}
</script>
@endsection