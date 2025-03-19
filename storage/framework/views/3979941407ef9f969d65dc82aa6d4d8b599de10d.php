<?php $__env->startSection('title', config('app.name', 'laravel'). ' | '.$title); ?>
<?php $__env->startSection('page-css'); ?>

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

                    <?php if(isset($quotations)): ?>

                    <div class="row">
                        <?php $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                                    <a href="<?php echo e(route('pms.supplier.profile',$quotation->relSuppliers->id)); ?>" target="_blank"><span>Rating:</span></a> <?php echo ratingGenerate($totalScore,$totalCount); ?>


                                                </div>
                                                <h5 class="review-count"></h5>
                                            </li>
                                            <li><strong><?php echo e(__('Supplier')); ?> :</strong> <?php echo e($quotation->relSuppliers->name); ?></li>
                                            <li><strong><?php echo e(__('Date')); ?> :</strong> <?php echo e(date('d-m-Y',strtotime($quotation->quotation_date))); ?></li>
                                        </ul>
                                    </div>
                                    <div class="col-6">
                                        <ul class="list-unstyled mb0 pull-right">

                                            <li><strong><?php echo e(__('Reference No')); ?>:</strong> <?php echo e($quotation->reference_no); ?></li>
                                            <li><strong><?php echo e(__('RFP No')); ?>:</strong> <?php echo e($quotation->relRequestProposal->reference_no); ?></li>

                                            <li>
                                                <div class="form-check">
                                                  <input class="form-check-input setRequiredOnSupplierPaymentTerm" type="checkbox" name="quotation_id[]" id="is_approved_<?php echo e($quotation->id); ?>" value="<?php echo e($quotation->id); ?>">
                                                  <input type="hidden" name="request_proposal_id" value="<?php echo e($quotation->request_proposal_id); ?>">
                                                  <label class="form-check-label" for="is_approved_<?php echo e($quotation->id); ?>">
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


                                    <?php $__currentLoopData = $quotation->relQuotationItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($key+1); ?></td>
                                        <td><?php echo e($item->relProduct->category->name); ?></td>
                                        <td><?php echo e($item->relProduct->name); ?> (<?php echo e(getProductAttributes($item->relProduct->id)); ?>)</td>
                                        <td><?php echo e($item->relProduct->productUnit->unit_name); ?></td>
                                        <td><?php echo e($item->unit_price); ?></td>
                                        <td><?php echo e($item->qty); ?></td>
                                        <td><?php echo e(number_format($item->total_price,2)); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <tr>
                                        <td colspan="4" class="text-right">Total</td>
                                        <td colspan=""><?php echo e(number_format($quotation->relQuotationItems->sum('unit_price'),2)); ?></td>
                                        <td colspan=""><?php echo e(number_format($quotation->relQuotationItems->sum('qty'),2)); ?></td>
                                        <td colspan=""><?php echo e(number_format($quotation->relQuotationItems->sum('total_price'),2)); ?></td>
                                    </tr>

                                    <tr>
                                        <td colspan="6" class="text-right">(-) Discount</td>
                                        <td><?php echo e(number_format($quotation->discount,2)); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-right">(+) Vat </td>
                                        <td><?php echo e(number_format($quotation->vat,2)); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-right"><strong>Total Amount</strong></td>
                                        <td><strong><?php echo e(number_format($quotation->gross_price,2)); ?></strong></td>
                                    </tr>


                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-4 form-group">
                                <label for="supplier_payment_terms_id"><strong>Supplier Payment Term</strong></label>
                                <select class="form-control" id="supplier_payment_terms_id<?php echo e($quotation->id); ?>" name="supplier_payment_terms_id[<?php echo e($quotation->id); ?>]">
                                    <?php if($quotation->relSuppliers->relPaymentTerms): ?>
                                    <option value="<?php echo e(null); ?>">Select Term</option>
                                    <?php $__currentLoopData = $quotation->relSuppliers->relPaymentTerms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($data->id); ?>"><?php echo e($data->relPaymentTerm->term); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-8 form-group">
                                <label for="note"><strong>Notes </strong>:</label>
                                <input type="text" name="note[<?php echo e($quotation->id); ?>]" placeholder="What is the reason for choosing this supplier?" id="note" class="form-control">

                            </div>

                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <?php endif; ?>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Submit</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

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
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/sites/34b/a/a55a29c214/procurement.zbridea.com/resources/views/pms/backend/pages/quotation/_compare2.blade.php ENDPATH**/ ?>