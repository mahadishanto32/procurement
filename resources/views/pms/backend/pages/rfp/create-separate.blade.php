@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
@endsection
@section('main-content')
@php
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request;
use App\Models\PmsModels\RequisitionDeliveryItem;
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
                    <div class="panel-body">
                        {!! Form::open(['route' => 'pms.rfp.request-proposal.separate.store',  'files'=> false, 'id'=>'', 'class' => 'form-horizontal']) !!}

                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="form-line">
                                        {!! Form::label('request_date', 'Date', array('class' => 'mb-1 font-weight-bold')) !!} 
                                        {!! Form::text('request_date',Request::old('request_date')?Request::old('request_date'):date('d-m-Y'),['id'=>'request_date','class' => 'form-control rounded air-datepicker','placeholder'=>'','readonly'=>'readonly']) !!}

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="form-line">
                                        {!! Form::label('reference_no', 'Ref No', array('class' => 'mb-1 font-weight-bold')) !!}
                                        {!! Form::text('reference_no',(Request::old('reference_no'))?Request::old('reference_no'):$refNo,['id'=>'reference_no','required'=>true,'class' => 'form-control rounded','readonly'=>'readonly']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="form-line">
                                        {!! Form::label('supplier_id', 'Select Supplier', array('class' => 'mb-1 font-weight-bold')) !!}
                                        {!! Form::Select('supplier_id[]', $supplierList ,Request::old('supplier_id'),['id'=>'supplier_id','multiple' => 'multiple', 'required'=>true,'class'=>'form-control rounded select2 select2-tags']) !!}

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="form-line">
                                        <br>

                                        <a href="{{URL::to('pms/rfp/request-proposal/create')}}" class="btn btn-sm btn-success text-white" data-toggle="tooltip" title="Back">
                                            <i class="las la-object-group"></i>Combine
                                        </a>

                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive style-scroll">
                            <table class="table table-striped table-bordered miw-500 dac_table" cellspacing="0" width="100%" id="dataTable">
                                <thead>
                                    <tr>
                                        <th width="5%">{{__('SL No.')}}</th>
                                        <th>{{__('Requisition Ref')}}</th>
                                        <th>{{__('Category')}}</th>
                                        <th>{{__('Items Name')}}</th>
                                        <th>{{__('Unit')}}</th>
                                        <th>{{__('Requisition Qty')}}</th>
                                        <th>{{__('Approved Qty')}}</th>
                                        <th width="10%">{{__('RFP Qty')}}</th>
                                        <th class="text-center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="chkbx_all_first" id="checkAllProduct" onclick="return CheckAll()">
                                                <label class="form-check-label mt-8" for="checkAllProduct">
                                                    <strong>All</strong>
                                                </label>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total_requisition_qty = 0;
                                        $total_approved_qty = 0;
                                        $total_sumOfRFP = 0;
                                    @endphp

                                    @if(count((array) $requisitions)>0)
                                    @foreach($requisitions as $key=>$requisition)
                                    @foreach($requisition->requisitionItems()->where('is_send','no')->get() as $rkey => $values)
                                    @php
                                    $sumOfSendRFP=RequisitionDeliveryItem::where('product_id',$values->product_id)
                                    ->whereHas('relRequisitionDelivery', function($query2) use($values){
                                            $query2->where('requisition_id',$values->requisition_id);
                                        })
                                    ->whereHas('relRequisitionDelivery.relRequisition', function($query){
                                        return $query->where('status', 1)
                                        ->where('request_status','send_rfp')
                                        ->whereHas('requisitionItems', function($query2){
                                            $query2->where('is_send','no');
                                        });
                                    })
                                    ->sum('delivery_qty');

                                    $subOfRFP = $values->qty-$sumOfSendRFP;

                                    $total_requisition_qty += $values->requisition_qty;
                                    $total_approved_qty += $values->qty;
                                    $total_sumOfRFP += $subOfRFP;
                                    @endphp
                                    <tr>
                                        @if($rkey == 0)
                                        <td rowspan="{{ $requisition->requisitionItems()->where('is_send','no')->count()}}">
                                            {{$key + 1}} 
                                        </td>
                                        <td rowspan="{{ $requisition->requisitionItems()->where('is_send','no')->count()}}">
                                           
                                            <a href="javascript:void(0)" data-src="{{route('pms.requisition.list.view.show',$requisition->id)}}" class="btn btn-link requisition m-1 rounded showRequistionDetails">{{ $requisition->reference_no }}</a>
                                        </td>
                                        @endif
                                        <td>
                                            {{$values->product->category->name?$values->product->category->name:''}}
                                        </td>
                                        <td>
                                            {{$values->product->name?$values->product->name:''}} ({{ getProductAttributes($values->product_id) }})
                                        </td>
                                        <td>
                                            {{$values->product->productUnit->unit_name?$values->product->productUnit->unit_name:''}}
                                        </td>
                                        <td>
                                            {{number_format($values->requisition_qty,0)}}
                                        </td>
                                        <td>
                                        {{$values->qty}}
                                            @if($sumOfSendRFP >0)
                                            <input type="hidden" name="qty[{{$values->product_id}}&{{$values->id}}][]" value="{{$values->qty-$sumOfSendRFP}}">
                                            @else
                                            <input type="hidden" name="qty[{{$values->product_id}}&{{$values->id}}][]" value="{{$values->qty}}">
                                            @endif
                                        </td>
                                        <td>
                                            <input type="number" name="request_qty[{{$values->product_id}}&{{$values->id}}][]" min="1" max="99999999" value="{{($sumOfSendRFP>0)?number_format($values->qty-$sumOfSendRFP,0): $values->qty}}" disabled class="form-control rounded request_qty" onchange="calculateTotal()" readonly onkeyup="calculateTotal()">
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="requisition_item_id[]"
                                            class="element_first" value="{{$values->id}}">
                                        </td>
                                    </tr>
                                    @endforeach
                                    
                                    @endforeach
                                    @endif
                                    
                                    <tr>
                                        <td colspan="5">Total:</td>
                                        <td>{{ $total_requisition_qty }}</td>
                                        <td>{{ $total_approved_qty }}</td>
                                        <td id="request_qty"></td>
                                        <td></td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-6">

                            </div>
                            <div class="col-md-3">

                             <label class="btn btn-xs rounded font-5 mt-10 pull-right ">
                                 <input type="checkbox" name="type" class="" value='online'> Allow Online Quotation
                             </label>

                         </div>
                         <div class="col-md-3">

                            {!! Form::submit('Send Procurement to supplier', ['class' => 'pull-right btn btn-success rounded font-10 mt-10','data-placement'=>'top','data-content'=>'click save changes button for send rfp']) !!}&nbsp;

                        </div>
                    </div>

                    {!! Form::close() !!}
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
                <h4 class="modal-title">Requisitions Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body" id="tableData"></div>
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

    function CheckAll() {

        if ($('#checkAllProduct').is(':checked')) {
            $('input.element_first').prop('checked', true);
        } else {
            $('input.element_first').prop('checked', false);
        }

        $.each($('.element_first'), function(index, val) {
            updateElement($(this));
        });
    }

    $('.element_first').click(function(event) {
        updateElement($(this));
    });

    function updateElement(element){
        if(element.is(':checked')){
            element.parent().prev().find('input').prop('disabled', false);
            element.parent().prev().prev().find('input').prop('disabled', false);
            element.prop('checked', true);
        }else{
            element.parent().prev().find('input').prop('disabled', true);
            element.parent().prev().prev().find('input').prop('disabled', true);
            element.prop('checked', false);
        }

        calculateTotal();
    }

    calculateTotal();
    function calculateTotal(){
        var total = 0;
        $.each($('.request_qty'), function(index, val) {
            if(!$(this).prop('disabled')){
                total += ($(this).val() != "" ? parseInt($(this).val()) : 0);
            }
        });

        $('#request_qty').html(total);
    }

</script>
@endsection
