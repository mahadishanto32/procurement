
<?php $__env->startSection('title', config('app.name', 'laravel'). ' | '.$title); ?>
<?php $__env->startSection('page-css'); ?>
<style type="text/css">
    .list-unstyled .ratings {
        display: none;
    }
</style>
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
                <li class="active">Gate-In</li>
                <li class="active"><?php echo e(__($title)); ?></li>

            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="<?php echo e($title); ?>" cellspacing="0" width="100%" id="dataTable">
                        <thead>
                            <tr>
                                <th width="5%"><?php echo e(__('SL No.')); ?></th>
                                <th><?php echo e(__('P.O. Date')); ?></th>
                                <th><?php echo e(__('Supplier')); ?></th>
                                <th><?php echo e(__('Reference No')); ?></th>
                                <th><?php echo e(__('P.O Qty')); ?></th>
                                <th><?php echo e(__('Gate-In Qty')); ?></th>
                                <th><?php echo e(__('Receive Status')); ?></th>
                                <th class="text-center"><?php echo e(__('Option')); ?></th>
                            </tr>
                        </thead>
                        <tbody id="viewResult">
                            <?php if(count($purchaseOrdersAgainstGrn)>0): ?>
                            <?php $__currentLoopData = $purchaseOrdersAgainstGrn; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($key + 1); ?></td>
                                <td><?php echo e(date('d-m-Y',strtotime($values->po_date))); ?></td>
                                <td><?php echo e($values->relQuotation?$values->relQuotation->relSuppliers->name:''); ?></td>
                                <td> <a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="<?php echo e(route('pms.purchase.order-list.show',$values->id)); ?>"><?php echo e($values->reference_no); ?></a></td>

                                <td><?php echo e($values->relPurchaseOrderItems->sum('qty')); ?></td>
                                <td><?php echo e($values->total_grn_qty); ?></td>

                                <td class="text-left">
                                    <?php if($values->relPurchaseOrderItems->sum('qty')==$values->total_grn_qty??0): ?>
                                        <button class="btn btn-default"><?php echo e(__('Full Received')); ?></button>
                                        <?php else: ?>
                                        <button class="btn btn-default"><?php echo e(__('Partial Received')); ?></button>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo e(route('pms.grn.grn-process.index')); ?><?php echo e('?po_id='.$values->id); ?>" target="_blank" class="btn btn-xs btn-success" data-toggle="tooltip" title="Click here to Grn List" > Gate-In(<?php echo e($values->relGoodReceiveNote->count()); ?>) </a>

                                    <a class="btn btn-primary btn-xs" href="<?php echo e(url('pms/grn/gate-in-slip/'.$values->id)); ?>" target="_blank"><i class="la la-print"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="col-12 py-2">
                        <?php if(count($purchaseOrdersAgainstGrn)>0): ?>
                        <ul>
                            <?php echo e($purchaseOrdersAgainstGrn->links()); ?>

                        </ul>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="POdetailsModel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Purchase Order</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body" id="body">

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
    (function ($) {
        "use script";

        const showPODetails = () => {
            $('.showPODetails').on('click', function () {

                $.ajax({
                    url: $(this).attr('data-src'),
                    type: 'get',
                    dataType: 'json',
                    data: '',
                })
                .done(function(response) {

                    if (response.result=='success') {
                        $('#POdetailsModel').find('#body').html(response.body);
                        $('#POdetailsModel').find('.modal-title').html(`Purchase Order Details`);
                        $('#POdetailsModel').modal('show');
                    }

                })
                .fail(function(response){
                    notify('Something went wrong!','error');
                });
            });
        }
        showPODetails();

    })(jQuery);
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/grn/po-list-against-grn.blade.php ENDPATH**/ ?>