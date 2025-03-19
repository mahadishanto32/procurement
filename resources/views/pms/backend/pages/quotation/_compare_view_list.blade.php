@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
<style type="text/css">
   
    .form-check-input{
        margin-top: -4px !important;
    }

</style>
@if(count($quotations)>2)
<style type="text/css">
    
    thead, tbody tr {
        display:table;
        width: 2000px;
        table-layout:fixed;
    }
    thead {
        width: calc( 2000px)
    } 
    ul {
        list-style: none;
    }
</style>
@endif
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
                   {!! Form::open(['route' => 'pms.quotation.quotations.cs.compare.approved',  'files'=> false, 'id'=>'', 'class' => '']) !!}
                    <div class="row">
                        @if($quotations)
                        @foreach($quotations as $key=>$quotation)
                        @if($key==0)
                        <div class="col-md-6">
                            <ul>
                                <li><strong>{{__('Request Proposal No')}} :</strong> {{$quotation->relRequestProposal->reference_no}}</li>
                                <li><strong>Project Name:</strong></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul>
                                <li><strong>{{__('RFP Provide By')}} :</strong> {{$quotation->relRequestProposal->createdBy->name}}</li>
                                <li><strong>{{__('RFP Date')}} :</strong> {{date('d-m-Y h:i:s A',strtotime($quotation->relRequestProposal->request_date))}}</li>
                            </ul>
                        </div>
                        @endif
                        @endforeach
                        <div class="col-md-12">

                            <div class="panel panel-info">

                                <div class="table-responsive">

                                    <table class="table table-bordered table-hover ">
                                        <thead>
                                            <tr>
                                                <th colspan="5">Party Name</th>
                                                @foreach($quotations as $q_key => $quotation)
                                                <?php 
                                                $TS = number_format($quotation->relSuppliers->SupplierRatings->sum('total_score'),2);
                                                $TC = $quotation->relSuppliers->SupplierRatings->count();

                                                $totalScore = isset($TS)?$TS:0;
                                                $totalCount = isset($TC)?$TC:0;
                                            ?>
                                            <th class="invoiceBody" colspan="2">

                                                <p class="ratings">

                                                    <a href="{{route('pms.supplier.profile',$quotation->relSuppliers->id)}}" target="_blank"><span>{{$quotation->relSuppliers->name}}</span></a> ({!!ratingGenerate($totalScore,$totalCount)!!})

                                                </p>

                                                <p><strong>{{__('Q:Ref:No')}}:</strong> {{$quotation->reference_no}}</p>

                                                <p>
                                                    <div class="form-check">
                                                      <input class="form-check-input" type="radio" name="quotation_id" id="is_approved_{{$quotation->id}}" value="{{$quotation->id}}" required {{ $q_key == 0 ? 'checked' : '' }} style="display: {{ $quotations->count() > 1 ? 'block' : 'none' }}">
                                                      <input type="hidden" name="request_proposal_id" value="{{$quotation->request_proposal_id}}">
                                                      <label class="form-check-label" for="is_approved_{{$quotation->id}}" style="margin-left: {{ $quotations->count() > 1 ? '0px' : '-20px' }}">
                                                        <strong>Approval</strong>
                                                    </label>
                                                </div>
                                            </p>

                                        </th>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Category</th>
                                        <th>Product</th>
                                        <th>Unit</th>
                                        <th class="text-right">Qty</th>
                                        @foreach($quotations as $quotation)
                                        <th>Unit Price</th>
                                        <th class="text-right">Item Total</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $total_qty=0;?>
                                    @if(isset($quotation->id))
                                    @foreach($quotation->relQuotationItems()->groupBy('product_id')->get() as $key=>$item)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$item->relProduct->category->name}}</td>
                                        <td>{{$item->relProduct->name}} ({{getProductAttributes($item->relProduct->id)}})</td>
                                        <td>{{$item->relProduct->productUnit->unit_name}}</td>
                                        <td class="text-right">{{$item->qty}}</td>

                                        @foreach($quotations as $key=>$quotation)
                                        <td>{{number_format(isset(\App\Models\PmsModels\QuotationsItems::where('product_id', $item->product_id)->where('quotation_id', $quotation->id)->first()->unit_price) ? \App\Models\PmsModels\QuotationsItems::where('product_id', $item->product_id)->where('quotation_id', $quotation->id)->first()->unit_price : 0,2)}}</td>
                                        <td class="text-right">{{number_format(isset(\App\Models\PmsModels\QuotationsItems::where('product_id', $item->product_id)->where('quotation_id', $quotation->id)->first()->total_price) ? \App\Models\PmsModels\QuotationsItems::where('product_id', $item->product_id)->where('quotation_id', $quotation->id)->first()->total_price : 0,2)}}</td>

                                        @endforeach

                                    </tr>
                                    <?php $total_qty +=$item->qty;?>
                                    @endforeach
                                    @endif
                                    
                                    <tr>
                                        <td colspan="4" class="text-right"><strong>Total</strong></td>
                                        <td class="text-right"><strong>{{$total_qty}}</strong></td>
                                        @foreach($quotations as $key=>$quotation)
                                        <td colspan=""><strong>{{number_format(\App\Models\PmsModels\QuotationsItems::where('quotation_id', $quotation->id)->sum('unit_price'),2)}}</strong></td>
                                        <td class="text-right"><strong>{{number_format(\App\Models\PmsModels\QuotationsItems::where('quotation_id', $quotation->id)->sum('total_price'),2)}}</strong></td>
                                        @endforeach
                                    </tr>

                                    <tr>
                                        <td colspan="5" class="text-right"></td>

                                        @foreach($quotations as $key=>$quotation)
                                        <?php 
                                        $total_price= \App\Models\PmsModels\QuotationsItems::where('quotation_id', $quotation->id)->sum('total_price'); 
                                    ?> 
                                    <td><strong>(-) Discount</strong></td>
                                    <td class="text-right"><strong>{{number_format($quotation->discount,2)}}</strong>
                                    </td>
                                    @endforeach
                                </tr>

                                <tr>
                                    <td colspan="5" class="text-right"></td>

                                    @foreach($quotations as $key=>$quotation)
                                    <?php 
                                    $total_price= \App\Models\PmsModels\QuotationsItems::where('quotation_id', $quotation->id)->sum('total_price'); 
                                ?> 
                                <td><strong>(+) Vat </strong></td>
                                <td class="text-right"><strong>{{$quotation->vat}}</strong>
                                </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right"></td>

                                @foreach($quotations as $key=>$quotation)
                                <?php 
                                $total_price= \App\Models\PmsModels\QuotationsItems::where('quotation_id', $quotation->id)->sum('total_price'); 
                            ?> 
                            <td><strong>Gross Total</strong></td>
                            <td class="text-right"><strong><?= number_format($quotation->gross_price,2); ?> </strong>
                            </td>
                            @endforeach
                        </tr>

                        <tr>
                            <td colspan="5"></td>
                            @foreach($quotations as $key=>$quotation)
                            <td><strong>Payment Term</strong></td>
                            <td class="text-left">
                                {{isset($quotation->relSupplierPaymentTerm->relPaymentTerm->term)?$quotation->relSupplierPaymentTerm->relPaymentTerm->term:''}}
                            </td>
                            @endforeach

                        </tr>

                        <tr>
                            <td colspan="5" class="text-right">Notes</td>
                            @foreach($quotations as $key=>$quotation)
                            <td colspan="2"><span>{!!$quotation->note!!}</span></td>

                            @endforeach

                        </tr>
                        <tr>
                            <td colspan="5" class="text-right">Remarks</td>
                            @foreach($quotations as $key=>$quotation)
                            <td colspan="2"><textarea class="form-control" name="remarks" rows="1" id="remarks" placeholder="What is the reason for choosing this supplier?">{!! $quotation->remarks?$quotation->remarks:'' !!}</textarea></td>

                            @endforeach

                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success">Submit</button>
            <a type="button" class="btn btn-danger" href="{{route('pms.quotation.approval.list')}}">Close</a>
        </div>
    </div>

</div>
@endif

{!! Form::close() !!}
</div>
</div>
</div>
</div>
</div>

@endsection