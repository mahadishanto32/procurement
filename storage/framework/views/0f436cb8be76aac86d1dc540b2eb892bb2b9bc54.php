
<?php $__env->startSection('title', config('app.name', 'laravel'). ' | '.$title); ?>
<?php $__env->startSection('page-css'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main-content'); ?>
    <?php
        use Illuminate\Support\Facades\URL;
        use Illuminate\Support\Facades\Request;
    ?>
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
                        <a href="<?php echo e(route('pms.rfp.request-proposal.create')); ?>" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add New Requisition" id="addRequestProposalBtn"> <i class="las la-plus"></i>Add</a>
                    </li>
                </ul><!-- /.breadcrumb -->

            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body">

                            <div class="table-responsive style-scroll">
                                <table class="table table-striped table-bordered miw-500 dac_table datatable-exportable" data-table-name="<?php echo e($title); ?>"cellspacing="0" width="100%" id="dataTable">
                                    <thead>
                                    <tr>
                                        <th width="5%"><?php echo e(__('SL No.')); ?></th>
                                        <th><?php echo e(__('Date')); ?></th>
                                        <th><?php echo e(__('RefNo')); ?></th>
                                        <th><?php echo e(__('Qty')); ?></th>
                                        <th><?php echo e(__('RFP Collection Type')); ?></th>
                                        <th><?php echo e(__('Created By')); ?></th>
                                        <th><?php echo e(__('Option')); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if(count($requestProposals) > 0): ?>
                                        <?php $__currentLoopData = $requestProposals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $requestProposal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $quotationSupplier=$requestProposal->relQuotations()->pluck('supplier_id')->all();

                                            $data=$requestProposal->defineToSupplier()->whereNotIn('supplier_id',$quotationSupplier)->get();
                                        ?>
                                        <?php if($data->count()>0): ?>
                                        <tr id="rowId<?php echo e($requestProposal->id); ?>">
                                                <td>
                                                   <?php echo e($key+1); ?>

                                                </td>
                                                <td>
                                                    <?php echo e(date('d-M-Y',strtotime($requestProposal->request_date))); ?>

                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" onclick="openModal(<?php echo e($requestProposal->id); ?>)"  class="btn btn-link"><?php echo e($requestProposal->reference_no); ?></a>
                                                </td>

                                                <td><?php echo e($requestProposal->requestProposalDetails->sum('request_qty')); ?></td>

                                                <td><?php echo e(ucfirst($requestProposal->type)); ?></td>
                                                <td><?php echo e($requestProposal->createdBy->name); ?></td>
                                                
                                                <td class="text-center action">
                                                    <div class="btn-group">
                                                        <button class="btn dropdown-toggle" data-toggle="dropdown">
                                                            <span id="statusName">
                                                                <?php echo e(__('Option')); ?>

                                                            </span>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a target="__blank" href="<?php echo e(route('pms.rfp.quotations.generate',$requestProposal->id)); ?>"><?php echo e(__('Quotation Generate')); ?></a>
                                                            </li>
                                                            <?php if($quotationSupplier): ?>
                                                            <li>
                                                                <a href="javascript:void(0)" class="completeQG" data-src="<?php echo e(route('pms.rfp.generate.complete')); ?>" data-id=<?php echo e($requestProposal->id); ?>><?php echo e(__('Complete')); ?></a>
                                                            </li>
                                                            <?php endif; ?>
                                                        </ul>
                                                    </div>

                                                </td>

                                            </tr>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7">No Data Found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>

                                
                                <div class="d-flex justify-content-lg-end">
                                    <?php echo $requestProposals->links(); ?>

                                </div>
                            </div>

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
            $('#modalContent').empty().load('<?php echo e(URL::to(Request()->route()->getPrefix()."request-proposal")); ?>/'+requestId);
            $('#requestProposalDetailModal').modal('show');
        }

        $('.completeQG').on('click', function () {
            let req_proposal_id = $(this).attr('data-id');
            swal({
                title: "<?php echo e(__('Are you sure?')); ?>",
                text: "<?php echo e(__('Once you Complete, You can not generate quotation from this proposal.')); ?>",
                icon: "warning",
                dangerMode: true,
                buttons: {
                    cancel: true,
                    confirm: {
                        text: "Complete",
                        value: true,
                        visible: true,
                        closeModal: true
                    },
                },
            }).then((value) => {
                if(value){
                $.ajax({
                    type: 'post',
                    url: $(this).attr('data-src'),
                    dataType: "json",
                    data:{_token: "<?php echo e(csrf_token()); ?>", req_proposal_id:req_proposal_id},
                    success:function (data) {
                        if(data.result == 'success'){
                            $('#rowId'+req_proposal_id).hide();
                             notify(data.message,data.result);
                        }else{
                            notify(data.message,data.result);
                        }
                    }
                });
                return false;
            }
        });
    });

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/rfp/index.blade.php ENDPATH**/ ?>