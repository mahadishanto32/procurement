
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
                    
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="5%"><?php echo e(__('SL No.')); ?></th>
                                    <th><?php echo e(__('Request Proposal')); ?></th>
                                    <th><?php echo e(__('Date')); ?></th>
                                    <th><?php echo e(__('Reference No')); ?></th>
                                    <th><?php echo e(__('Supplier')); ?></th>
                                    <th><?php echo e(__('Total Price')); ?></th>
                                    <th><?php echo e(__('Discount ')); ?></th>
                                    <th><?php echo e(__('Vat ')); ?></th>
                                    <th><?php echo e(__('Gross Price')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Quotation Type')); ?></th>
                                    <th class="text-center"><?php echo e(__('Option')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($proposals)>0): ?>
                                <?php $__currentLoopData = $proposals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pkey=> $proposal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($proposal->relQuotations->count() > 0): ?>
                                    <?php $__currentLoopData = $proposal->relQuotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rkey => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($rkey+ 1); ?></td>
                                        <?php if($rkey == 0): ?>
                                        <td rowspan="<?php echo e($proposal->relQuotations->count()); ?>"><a href="javascript:void(0)" onclick="openRequestProposalModal(<?php echo e($values->request_proposal_id); ?>)"  class="btn btn-link"><?php echo e($values->relRequestProposal->reference_no); ?></a></td>
                                        <?php endif; ?>

                                        <td><?php echo e(date('d-m-Y',strtotime($values->quotation_date))); ?></td>
                                        <td><a href="javascript:void(0)" onclick="openModal(<?php echo e($values->id); ?>)"  class="btn btn-link"><?php echo e($values->reference_no); ?></a></td>
                                        <td><?php echo e($values->relSuppliers->name); ?></td>
                                        
                                        <td><?php echo e($values->total_price); ?></td>
                                        <td><?php echo e($values->discount); ?> </td>
                                        <td><?php echo e($values->vat); ?> </td>
                                        <td><?php echo e($values->gross_price); ?></td>
                                        <td><?php echo e(ucfirst($values->status)); ?></td>
                                        <td><?php echo e(ucfirst($values->type)); ?></td>
                                        
                                        <td class="text-center action">
                                           <a href="javascript:void(0)" onclick="openModal(<?php echo e($values->id); ?>)"  class="btn btn-info"><i class="las la-eye"></i></a>

                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </tbody>
                           
                        </table>
                        <div class="p-3">
                            <?php if(count($proposals)>0): ?>
                                <ul>
                                    <?php echo e($proposals->links()); ?>

                                </ul>

                                <?php endif; ?>
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
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-script'); ?>
<script>

   function openModal(quotation_id) {
    $('#tableData').load('<?php echo e(URL::to(Request()->route()->getPrefix()."/quotation-items")); ?>/'+quotation_id);
    $('#requisitionDetailModal').find('.modal-title').html(`Quotation Details`);
    $('#requisitionDetailModal').modal('show');
}

 function openRequestProposalModal(id) {
        $('#tableData').load('<?php echo e(URL::to('pms/rfp/request-proposal')); ?>/'+id);
        $('#requisitionDetailModal').find('.modal-title').html(`Proposal Details`);
        $('#requisitionDetailModal').modal('show')
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/quotation/index.blade.php ENDPATH**/ ?>