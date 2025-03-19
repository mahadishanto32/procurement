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
                <div class="panel panel-body">
                    {!! Form::open(['route' => 'pms.quotation.quotations.cs.compare.store',  'files'=> false, 'id'=>'', 'class' => '']) !!}
                    @if(isset($quotations))

                    <div class="row">
                        @foreach($quotations as $quotation)
                        <?php 
                        $TS = number_format($quotation->relSuppliers->SupplierRatings->sum('total_score'),2);
                        $TC = $quotation->relSuppliers->SupplierRatings->count();

                        $totalScore = isset($TS)?$TS:0;
                        $totalCount = isset($TC)?$TC:0;
                    ?>

                    <div class="col-md-<?=$quotations->count()>1?6:12?>">
                        <div class="panel panel-info">

                            <div class="col-lg-12 invoiceBody">
                                <div class="invoice-details mt25 row">

                                    <div class="well col-6">
                                        <ul class="list-unstyled mb0">
                                            <li>

                                                <div class="ratings">
                                                    <a href="{{route('pms.supplier.profile',$quotation->relSuppliers->id)}}" target="_blank"><span>Rating:</span></a> {!!ratingGenerate($totalScore,$totalCount)!!}

                                                </div>
                                                <h5 class="review-count"></h5>
                                            </li>
                                            <li><strong>{{__('Supplier')}} :</strong> {{$quotation->relSuppliers->name}}</li>
                                            <li><strong>{{__('Date')}} :</strong> {{date('d-m-Y',strtotime($quotation->quotation_date))}}</li>
                                        </ul>
                                    </div>
                                    <div class="col-6">
                                        <ul class="list-unstyled mb0 pull-right">

                                            <li><strong>{{__('Reference No')}}:</strong> {{$quotation->reference_no}}</li>
                                            <li><strong>{{__('RFP No')}}:</strong> {{$quotation->relRequestProposal->reference_no}}</li>

                                            <li>
                                                <div class="form-check">
                                                  <input class="form-check-input setRequiredOnSupplierPaymentTerm" type="checkbox" name="quotation_id[]" id="is_approved_{{$quotation->id}}" value="{{$quotation->id}}">
                                                  <input type="hidden" name="request_proposal_id" value="{{$quotation->request_proposal_id}}">
                                                  <label class="form-check-label" for="is_approved_{{$quotation->id}}">
                                                    <strong>Request For Approval</strong>
                                                </label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                        <div class="table-responsive">

                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Category</th>
                                        <th>Product</th>
                                        <th>Unit</th>
                                        <th>Unit Price</th>
                                        <th>Qty</th>
                                        <th>Item Total</th>
                                    </tr>
                                </thead>
                                <tbody>


                                    @foreach($quotation->relQuotationItems as $key=>$item)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$item->relProduct->category->name}}</td>
                                        <td>{{$item->relProduct->name}} ({{ getProductAttributes($item->relProduct->id) }})</td>
                                        <td>{{$item->relProduct->productUnit->unit_name}}</td>
                                        <td>{{$item->unit_price}}</td>
                                        <td>{{$item->qty}}</td>
                                        <td>{{number_format($item->total_price,2)}}</td>
                                    </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="4" class="text-right">Total</td>
                                        <td colspan="">{{number_format($quotation->relQuotationItems->sum('unit_price'),2)}}</td>
                                        <td colspan="">{{number_format($quotation->relQuotationItems->sum('qty'),2)}}</td>
                                        <td colspan="">{{number_format($quotation->relQuotationItems->sum('total_price'),2)}}</td>
                                    </tr>

                                    <tr>
                                        <td colspan="6" class="text-right">(-) Discount</td>
                                        <td>{{number_format($quotation->discount,2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-right">(+) Vat </td>
                                        <td>{{number_format($quotation->vat,2)}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-right"><strong>Total Amount</strong></td>
                                        <td><strong>{{number_format($quotation->gross_price,2)}}</strong></td>
                                    </tr>


                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-4 form-group">
                                <label for="supplier_payment_terms_id"><strong>Supplier Payment Term</strong></label>
                                <select class="form-control" id="supplier_payment_terms_id{{$quotation->id}}" name="supplier_payment_terms_id[{{$quotation->id}}]">
                                    @if($quotation->relSuppliers->relPaymentTerms)
                                    <option value="{{ null }}">Select Term</option>
                                    @foreach($quotation->relSuppliers->relPaymentTerms as $data)
                                    <option value="{{$data->id}}">{{$data->relPaymentTerm->term}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-8 form-group">
                                <label for="note"><strong>Notes </strong>:</label>
                                <input type="text" name="note[{{$quotation->id}}]" placeholder="What is the reason for choosing this supplier?" id="note" class="form-control">

                            </div>

                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @endif

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Submit</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>
</div>
</div>
@endsection
@section('page-script')
<script>
    (function ($) {
        "use script";

        const setRequiredOnSupplierPaymentTerm = () => {
            $('.setRequiredOnSupplierPaymentTerm').on('click', function () {
                let quotationId = $(this).val();
                if (quotationId){
                    if ($('#supplier_payment_terms_id'+quotationId).attr("required")=='required') {
                        console.log('false');
                        $('#supplier_payment_terms_id'+quotationId).attr("required", false);
                    }else{
                        console.log('true');
                        $('#supplier_payment_terms_id'+quotationId).attr("required", true);
                    }
                }
            });
        };

        setRequiredOnSupplierPaymentTerm();
    })(jQuery)
</script>
@endsection