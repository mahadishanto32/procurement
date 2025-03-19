<?php $__env->startSection('title', config('app.name', 'laravel'). ' | '.$title); ?>
<?php $__env->startSection('page-css'); ?>
<style type="text/css">

    .form-check-input{
        margin-top: -4px !important;
    }

</style>
<?php if(count($quotations)>2): ?>
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
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main-content'); ?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="<?php echo e(route('pms.dashboard')); ?>"><?php echo e(__('Home')); ?></a>
                </li>
                <li>
                    <a href="#">PMS</a>
                </li>
                <li class="active"><?php echo e(__($title)); ?></li>
                <li class="top-nav-btn">
                    <a href="javascript:history.back()" class="btn btn-sm btn-warning text-white" data-toggle="tooltip" title="Back" > <i class="las la-chevron-left"></i>Back</a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-body">
                    <?php echo Form::open(['route' => 'pms.quotation.quotations.cs.compare.store',  'files'=> false, 'id'=>'', 'class' => '']); ?>

                    <div class="row">
                        <?php if($quotations): ?>
                        <?php $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($key==0): ?>
                        <div class="col-md-6">
                            <ul>
                                <li><strong><?php echo e(__('Request Proposal No')); ?> :</strong> <?php echo e($quotation->relRequestProposal->reference_no); ?></li>
                                <li><strong>Project Name:</strong></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul>
                                <li><strong><?php echo e(__('RFP Provide By')); ?> :</strong> <?php echo e($quotation->relRequestProposal->createdBy->name); ?></li>
                                <li><strong><?php echo e(__('RFP Date')); ?> :</strong> <?php echo e(date('d-m-Y h:i:s A',strtotime($quotation->relRequestProposal->request_date))); ?></li>
                            </ul>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-12">

                            <div class="panel panel-info">

                                <div class="table-responsive">

                                    <table class="table table-bordered table-hover ">
                                        <thead>
                                            <tr>
                                                <th colspan="5">Party Name</th>
                                                <?php $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php 
                                                $TS = number_format($quotation->relSuppliers->SupplierRatings->sum('total_score'),2);
                                                $TC = $quotation->relSuppliers->SupplierRatings->count();

                                                $totalScore = isset($TS)?$TS:0;
                                                $totalCount = isset($TC)?$TC:0;
                                            ?>
                                            <th class="invoiceBody" colspan="2">

                                                <p class="ratings">

                                                    <a href="<?php echo e(route('pms.supplier.profile',$quotation->relSuppliers->id)); ?>" target="_blank"><span><?php echo e($quotation->relSuppliers->name); ?></span></a> (<?php echo ratingGenerate($totalScore,$totalCount); ?>)

                                                </p>

                                                <p><strong><?php echo e(__('Q:Ref:No')); ?>:</strong> <?php echo e($quotation->reference_no); ?></p>

                                                <p>
                                                    <div class="form-check">
                                                      <input class="form-check-input setRequiredOnSupplierPaymentTerm" type="checkbox" name="quotation_id[]" id="is_approved_<?php echo e($quotation->id); ?>" value="<?php echo e($quotation->id); ?>">
                                                      <input type="hidden" name="request_proposal_id" value="<?php echo e($quotation->request_proposal_id); ?>">
                                                      <label class="form-check-label" for="is_approved_<?php echo e($quotation->id); ?>">
                                                        <strong>Request For Approval</strong>
                                                    </label>
                                                </div>
                                            </p>

                                        </th>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                    <tr>
                                        <th width="1%">Sl</th>
                                        <th>Category</th>
                                        <th >Product</th>
                                        <th>Unit</th>
                                        <th class="text-right">Qty</th>
                                        <?php $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th>Unit Price</th>
                                        <th class="text-right">Item Total</th>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $total_qty=0;?>
                                    <?php $__currentLoopData = $quotation->relQuotationItems()->groupBy('product_id')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($key+1); ?></td>
                                        <td><?php echo e($item->relProduct->category->name); ?></td>
                                        <td><?php echo e($item->relProduct->name); ?> (<?php echo e(getProductAttributes($item->relProduct->id)); ?>)</td>
                                        <td><?php echo e($item->relProduct->productUnit->unit_name); ?></td>
                                        <td class="text-right"><?php echo e($item->qty); ?></td>

                                        <?php $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td><?php echo e(number_format(\App\Models\PmsModels\QuotationsItems::where('product_id', $item->product_id)->where('quotation_id', $quotation->id)->first()->unit_price,2)); ?></td>
                                        <td class="text-right"><?php echo e(number_format(\App\Models\PmsModels\QuotationsItems::where('product_id', $item->product_id)->where('quotation_id', $quotation->id)->first()->total_price,2)); ?></td>

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    </tr>
                                    <?php $total_qty +=$item->qty;?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    
                                    <tr>
                                        <td colspan="4" class="text-right"><strong>Total</strong></td>
                                        <td class="text-right"><strong><?php echo e($total_qty); ?></strong></td>
                                        <?php $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td colspan=""><strong><?php echo e(number_format(\App\Models\PmsModels\QuotationsItems::where('quotation_id', $quotation->id)->sum('unit_price'),2)); ?></strong></td>
                                        <td class="text-right"><strong><?php echo e(number_format(\App\Models\PmsModels\QuotationsItems::where('quotation_id', $quotation->id)->sum('total_price'),2)); ?></strong></td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>

                                    <tr>
                                        <td colspan="5" class="text-right"></td>

                                        <?php $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php 
                                        $total_price= \App\Models\PmsModels\QuotationsItems::where('quotation_id', $quotation->id)->sum('total_price'); 
                                    ?> 
                                    <td><strong>(-) Discount</strong></td>
                                    <td class="text-right"><strong> <?= number_format(($quotation->discount),2); ?></strong>
                                    </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>

                                <tr>
                                    <td colspan="5" class="text-right"></td>

                                    <?php $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php 
                                    $total_price= \App\Models\PmsModels\QuotationsItems::where('quotation_id', $quotation->id)->sum('total_price'); 
                                ?> 
                                <td><strong>(+) Vat</strong></td>
                                <td class="text-right"><strong> <?php echo e($quotation->vat); ?></strong>
                                </td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right"></td>

                                <?php $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php 
                                $total_price= \App\Models\PmsModels\QuotationsItems::where('quotation_id', $quotation->id)->sum('total_price'); 
                            ?> 
                            <td><strong>Gross Total</strong></td>
                            <td class="text-right"><strong><?= number_format($quotation->gross_price,2); ?> </strong>
                            </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>

                        <tr>
                            <td colspan="5"></td>
                            <?php $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td><strong>Supplier Payment Term</strong></td>
                            <td class="text-left">
                                <select class="form-control" id="supplier_payment_terms_id<?php echo e($quotation->id); ?>" name="supplier_payment_terms_id[<?php echo e($quotation->id); ?>]">
                                    <?php if($quotation->relSuppliers->relPaymentTerms): ?>
                                    <option value="<?php echo e(null); ?>">Select Term</option>
                                    <?php $__currentLoopData = $quotation->relSuppliers->relPaymentTerms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($data->id); ?>"><?php echo e($data->relPaymentTerm->term); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </tr>

                        <tr>
                            <td colspan="5"></td>
                            <?php $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td colspan="2"><textarea class="form-control" name="note[<?php echo e($quotation->id); ?>]" rows="1" id="note" placeholder="What is the reason for choosing this supplier?"></textarea></td>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success">Submit</button>
            <a type="button" class="btn btn-danger" href="<?php echo e(route('pms.quotation.quotations.cs.analysis')); ?>">Close</a>
        </div>
    </div>

</div>
<?php endif; ?>

<?php echo Form::close(); ?>

</div>
</div>
</div>
</div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-script'); ?>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/sites/34b/a/a55a29c214/procurement.zbridea.com/resources/views/pms/backend/pages/quotation/_compare.blade.php ENDPATH**/ ?>