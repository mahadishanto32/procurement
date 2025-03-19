@if ($grn->rel_goods_received_items->count() >0)
        
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
                <li class="top-nav-btn">

                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div>

                <div class="row">

                    <?php 
                    $TS = number_format($grn->relPurchaseOrder->relQuotation->relSuppliers->SupplierRatings->sum('total_score'),2);
                    $TC = $grn->relPurchaseOrder->relQuotation->relSuppliers->SupplierRatings->count();

                    $totalScore = isset($TS)?$TS:0;
                    $totalCount = isset($TC)?$TC:0;
                ?>

                <div class="col-md-12">
                    <div class="panel panel-info">

                        <div class="col-lg-12 invoiceBody">
                            <div class="invoice-details mt25 row">

                                <div class="well col-6">
                                    <ul class="list-unstyled mb0">
                                        <li>
                                            <div class="ratings">
                                                <a href="{{route('pms.supplier.profile',$grn->relPurchaseOrder->relQuotation->relSuppliers->id)}}" target="_blank"><span>Rating:</span></a> {!!ratingGenerate($totalScore,$totalCount)!!}
                                            </div>
                                            <h5 class="review-count"></h5>
                                        </li>
                                        <li><strong>{{__('Supplier') }} :</strong> {{$grn->relPurchaseOrder->relQuotation->relSuppliers->name}}</li>
                                        <li><strong>{{__('Email')}} :</strong> {{$grn->relPurchaseOrder->relQuotation->relSuppliers->email}}</li>
                                        <li><strong>{{__('Phone')}} :</strong> {{$grn->relPurchaseOrder->relQuotation->relSuppliers->phone}}</li>
                                        <li><strong>{{__('Address')}}:</strong> {{$grn->relPurchaseOrder->relQuotation->relSuppliers->address}}</li>

                                    </ul>
                                </div>
                                <div class="col-6">
                                    <ul class="list-unstyled mb0 pull-right">
                                        <li><strong>{{__('Date')}} :</strong> {{date('d-m-Y',strtotime($grn->received_date))}}</li>
                                        <li><strong>{{__('GRN Reference No.')}}:</strong> {{$grn->reference_no}}</li>
                                        <li><strong>{{__('Challan No.')}}:</strong> {{$grn->challan}}</li>
                                        <li><strong>{{__('Receive Qty.')}}:</strong> {{$grn->rel_goods_received_items->sum('qty')}}</li>
                                        <li><strong>{{__('Receive Status.')}}:</strong> <span class="capitalize"> {{$grn->received_status}}</span></li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">

                                <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>Sl No.</th>
                                            <th>Category</th>
                                            <th>Product</th>
                                            <th>Unit</th>
                                            <th>Unit Price</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                           
                                            <th class="text-center">Option</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $sumOfLeftQty=0; $sumOfUnitPrice=0; ?>
                                        @foreach($grn->rel_goods_received_items as $key=>$item)
                                        <?php 
                                        $sumOfLeftQty +=($item->qty-$item->received_qty);
                                        $sumOfUnitPrice +=$item->sub_total;
                                        ?>
                                
                                <tr id="removeApprovedRow{{$item->id}}">
                                    <td>{{$key+1}}</td>
                                    <td>{{$item->relProduct->category->name}}</td>
                                    <td>{{$item->relProduct->name}} ({{ getProductAttributes($item->relProduct->id) }})</td>
                                    <td>{{$item->relProduct->productUnit->unit_name}}</td>
                                    <td>{{number_format($item->unit_amount,2)}}</td>
                                    <td>{{number_format($item->qty,0)}}</td>
                                    <td>{{number_format($item->sub_total,2)}}</td>
                                    
                                   
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button class="btn dropdown-toggle" data-toggle="dropdown">
                                                <span id="statusName{{$item->id}}" title="Click Here To Quality Ensure">
                                                    {{ucwords($item->quality_ensure)}}
                                                </span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    @if($item->quality_ensure=='pending')
                                                    @can('quality-ensure-approved')
                                                    <li>
                                                        <a href="javascript:void(0)" title="Click Here To Approved" class="qualityEnsureStatusChange" data-id="{{$item->id}}" data-category-id="{{ $item->relProduct->category_id }}" data-status="approved">{{ __('Approved')}}
                                                        </a>
                                                    </li>
                                                    @endcan
                                                    @endif

                                                    @can('quality-ensure-return')
                                                    <li>
                                                        <a href="javascript:void(0)" title="Click Here To Return" class="qualityEnsureStatusChange" data-id="{{$item->id}}" data-category-id="{{ $item->relProduct->category_id }}" data-qty="{{$item->qty}}" data-title="Return Qty" data-status="return">{{ __('Return')}}
                                                        </a>
                                                    </li>
                                                    @endcan

                                                    @can('quality-ensure-return-change')
                                                    <li>
                                                        <a href="javascript:void(0)" title="Click Here To Replace" class="qualityEnsureStatusChange" data-id="{{$item->id}}" data-category-id="{{ $item->relProduct->category_id }}" data-qty="{{$item->qty}}" data-title="Replace Qty" data-status="return-change">{{ __('Replace')}}
                                                        </a>
                                                    </li>
                                                    @endcan

                                                </li>
                                            </ul>

                                        </div>
                                    </td>
                                </tr>
                                
                                @endforeach
                                <tr>
                                    <td colspan="4" class="text-right">Total</td>
                                    <td colspan="">{{isset($grn->rel_goods_received_items)?number_format($grn->rel_goods_received_items->sum('unit_amount'),0):0}}</td>
                                    <td>{{isset($sumOfLeftQty)?number_format($sumOfLeftQty,0):0}}</td>
                                    <td colspan="">{{isset($sumOfUnitPrice)?number_format($sumOfUnitPrice,2):0}}</td>

                                    <td></td>
                                    
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group">
                        <label for="remarks"><strong>Notes</strong>:</label>
                        <span>{!! $grn->note?$grn->note:'' !!}</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>

<div class="modal" id="qualityensureModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Quality Ensure</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <form action="{{route('pms.quality.ensure.status.save')}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                   <label for="total_qty">Total Qty :</label>
                                   <input type="number" class="form-control" placeholder="0" name="total_qty" id="dataQty" readonly>
                                </div>
                                <div class="col-md-4">
                                   <label for="received_qty">Received Qty :</label>
                                   <input type="number" class="form-control" placeholder="0" name="received_qty" id="receivedQty" onchange="checkReceivedQuantity($(this))" onkeyup="checkReceivedQuantity($(this))" min="0" max="9999999" value="0"> 
                                </div>
                                <div class="col-md-4">
                                    <label for="return_qty" id="dataTitle"> Qty :</label>
                                    <input type="number" class="form-control" placeholder="0" required name="return_qty" id="returnQty" value="0" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-group browsers">
                                <label for="faq_id">Select Reason :</label>
                                <ul id="faq-view">
                                    
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label for="return_note">Notes :</label>
                                <textarea class="form-control" name="return_note" rows="3" id="return_note" placeholder="Write down here reason for return"></textarea>

                                <input type="hidden" readonly required name="id" id="goodsReceiveItemsId">
                                <input type="hidden" readonly required name="quality_ensure" id="QualityEnsureStatus">
                            </div>
                        </div>
                        
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('page-script')
<script>
    (function ($) {
        "use script";

        $('.qualityEnsureStatusChange').on('click', function () {

            let id = $(this).attr('data-id');
            let quality_ensure = $(this).attr('data-status');
            
            if(quality_ensure==='return-change' || quality_ensure==='return'){
                
                $('#goodsReceiveItemsId').val(id);
                $('#QualityEnsureStatus').val(quality_ensure);
                $('#dataTitle').html($(this).attr('data-title'));
                $('#dataQty').val($(this).attr('data-qty'));
                $('#returnQty').val($(this).attr('data-qty'));

                $('#faq-view').html('');
                $.ajax({
                    url: "{{ url('pms/quality-ensure/ensure-check-get-faqs') }}/"+$(this).attr('data-category-id'),
                    type: 'GET',
                    data: {},
                })
                .done(function(response) {
                    $('#faq-view').html(response);
                    removeRequired();
                });
                
                return $('#qualityensureModal').modal('show').on('hidden.bs.modal', function (e) {
                    let form = document.querySelector('#qualityensureModal').querySelector('form').reset();
                });
                
            }else if(quality_ensure==='approved'){
                    swal({
                        title: "{{__('Are you sure?')}}",
                        text: 'Would you like to ensure the quality of this product & approved It?',
                        icon: "warning",
                        dangerMode: true,
                        buttons: {
                            cancel: true,
                            confirm: {
                                text: 'Approved',
                                value: true,
                                visible: true,
                                closeModal: true
                            },
                        },
                    }).then((value) => {
                        if(value){
                            $.ajax({
                                url: "{{ url('pms/quality-ensure/ensure-status-save') }}",
                                type: 'POST',
                                dataType: 'json',
                                data: {_token: "{{ csrf_token() }}", id:id,quality_ensure:quality_ensure},
                            })
                            .done(function(response) {
                                if(response.success){

                                    $('#statusName'+id).html(response.new_text);
                                    $('#removeApprovedRow'+id).hide();

                                    notify(response.message,'success');
                                }else{
                                    notify(response.message,'error');
                                }
                            })
                            .fail(function(response){
                                notify('Something went wrong!','error');
                            });

                            return false;
                        }
                    });
            }

        });

        const removeRequired = () => {
            let requiredCheckboxes = $('.browsers :checkbox[required]');
            requiredCheckboxes.on('change', function () {
                if(requiredCheckboxes.is(':checked')) {
                    requiredCheckboxes.removeAttr('required');
                } else {
                    requiredCheckboxes.attr('required', 'required');
                }
            });
        };

        removeRequired();

    })(jQuery);

     function  checkReceivedQuantity(element){
       var dataQty = parseInt(element.parent().prev().find('input').val() != "" ? element.parent().prev().find('input').val() : 0);
       var receivedQty = parseInt(element.val() !="" ? element.val() : 0);
       console.log(receivedQty);
       console.log(dataQty);
       if (receivedQty > dataQty) {
            notify('Received Qty Must be Less Than or equal Total Qty.','error');
            element.val(dataQty-1);
       }else{
            element.parent().next().find('input').val(dataQty-receivedQty);
       } 
    }
</script>
@endsection
@else
<script type="text/javascript">
    window.location = "{{ url('pms/grn/grn-process') }}";
</script>
@endif