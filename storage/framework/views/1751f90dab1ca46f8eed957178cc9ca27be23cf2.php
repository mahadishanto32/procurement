
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
                <div class="panel panel-info">
                    <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                        <thead>
                            <tr>
                                <th width="5%"><?php echo e(__('SL No.')); ?></th>
                                <th><?php echo e(__('Request Proposal')); ?></th>
                                
                                <th><?php echo e(__('Supplier')); ?></th>
                                <th class="text-center"><?php echo e(__('Option')); ?></th>
                            </tr>
                        </thead>
                        <tbody >
                            <?php if(count($quotations)>0): ?>
                            <?php $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e(($quotations->currentpage()-1) * $quotations->perpage() + $key + 1); ?></td>

                                <td><a href="javascript:void(0)" onclick="openModal(<?php echo e($values->relRequestProposal->id); ?>)"  class="btn btn-link"><?php echo e($values->relRequestProposal->reference_no); ?></a></td>


                                <td>
                                    <?php if($values->relSelfQuotationSupplierByProposalId): ?>
                                    <?php $__currentLoopData = $values->relSelfQuotationSupplierByProposalId()->where('is_approved','pending')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <button class="btn btn-sm btn-primary"><?php echo e($supplier->relSuppliers->name); ?></button>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center action">
                                   <a href="<?php echo e(route('pms.quotation.quotations.cs.compare',$values->request_proposal_id)); ?>"  title="Compare Process Analysis"  class="btn btn-success"><i class="las la-border-all"></i></a>

                                   <a href="<?php echo e(route('pms.quotation.quotations.cs.compare.list',$values->request_proposal_id)); ?>"  title="Compare Process Analysis"  class="btn btn-success"><i class="las la-list"></i></a>

                               </td>
                           </tr>
                           <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                           <?php endif; ?>
                       </tbody>

                   </table>
                   <div class="p-3">
                    <?php if(count($quotations)>0): ?>
                    <ul>
                        <?php echo e($quotations->links()); ?>

                    </ul>

                    <?php endif; ?>
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
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-script'); ?>
<script>
    function openModal(requestId) {
        $('#modalContent').empty().load('<?php echo e(URL::to("pms/rfp/request-proposal")); ?>/'+requestId);
        $('#requestProposalDetailModal').modal('show')
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/quotation/analysis-index.blade.php ENDPATH**/ ?>