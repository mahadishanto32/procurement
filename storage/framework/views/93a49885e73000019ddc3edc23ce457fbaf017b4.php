
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
                <li class="active"><?php echo e(__($title)); ?></li>
                <li class="top-nav-btn">
                   
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="panel panel-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <p class="mb-1 font-weight-bold"><label for="from_date"><?php echo e(__('From Date')); ?>:</label></p>
                        <div class="input-group input-group-md mb-3 d-">
                            <input type="text" name="from_date" id="from_date" class="search-datepicker form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required readonly value="<?php echo e(old('from_date')?old('from_date'):date("d-m-Y", strtotime(date('Y-m-01')))); ?>">
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <p class="mb-1 font-weight-bold"><label for="to_date"><?php echo e(__('To Date')); ?>:</label></p>
                        <div class="input-group input-group-md mb-3 d-">
                            <input type="text" name="to_date" id="to_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required readonly value="<?php echo e(old('to_date')?old('to_date'):date('d-m-Y')); ?>">
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <p class="mb-1 font-weight-bold"><label for="received_status"><?php echo e(__('Received Status')); ?>:</label></p>
                        <div class="input-group input-group-md mb-3 d-">
                            <select name="received_status" id="received_status" class="form-control rounded">
                                <option value="<?php echo e(null); ?>"><?php echo e(__('Select One')); ?></option>
                                <option value="full">Full Received</option>
                                <option value="partial">Partial Received</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <p class="mb-1 font-weight-bold"><label for="searchGRNList"></label></p>
                        <div class="input-group input-group-md">
                            <a href="javascript:void(0)" class="btn btn-success rounded mt-8" data-src="<?php echo e(route('pms.grn.grn-process.search')); ?>" id="searchGRNList"> <i class="las la-search"></i>Search</a>
                        </div>
                    </div>
                </div>
            
                <div id="viewResult">


                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('SL No.')); ?></th>
                                    <th><?php echo e(__('P.O Reference')); ?></th>
                                    <th><?php echo e(__('P.O Date')); ?></th>
                                    <th><?php echo e(__('Chalan No')); ?></th>
                                    <th><?php echo e(__('Gate-In Reference')); ?></th>
                                    <th><?php echo e(__('Gate-In Date')); ?></th>
                                    <th><?php echo e(__('Po Qty')); ?></th>
                                    <th><?php echo e(__('Gate in Qty')); ?></th>
                                    <th><?php echo e(__('Receive Status')); ?></th>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('quality-ensure')): ?>
                                    <th><?php echo e(__('Quality Ensure')); ?></th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>

                                <?php if(count($purchaseOrder)>0): ?>
                                <?php $__currentLoopData = $purchaseOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pkey=> $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($po->relGoodReceiveNote->count() > 0): ?>
                                <?php $__currentLoopData = $po->relGoodReceiveNote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rkey => $grn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <?php if($rkey == 0): ?>
                                    <td rowspan="<?php echo e($po->relGoodReceiveNote->count()); ?>"><?php echo e(($purchaseOrder->currentpage()-1) * $purchaseOrder->perpage() + $pkey + 1); ?></td>

                                    <td rowspan="<?php echo e($po->relGoodReceiveNote->count()); ?>">
                                        <a href="javascript:void(0)" class="btn btn-link showGateInPODetails" data-src="<?php echo e(route('pms.purchase.order-list.show',$grn->relPurchaseOrder->id)); ?>" data-title="Purchase Order Details"><?php echo e($grn->relPurchaseOrder->reference_no); ?>

                                        </a>
                                    </td>
                                    <td rowspan="<?php echo e($po->relGoodReceiveNote->count()); ?>">
                                        <?php echo e(date('d-M-Y',strtotime($po->po_date))); ?>

                                    </td>
                                    <?php endif; ?>
                                    <td><?php echo e($grn->challan); ?></td>
                                    <td>
                                        <a href="javascript:void(0)" class="btn btn-link showGateInPODetails" data-src="<?php echo e(route('pms.grn.grn-process.show',$grn->id)); ?>" data-title="Gate-In Details"><?php echo e($grn->reference_no); ?>

                                        </a>
                                        <a class="btn btn-primary btn-xs" href="<?php echo e(url('pms/grn/gate-in-slip/'.$po->id.'?grn='.$grn->id)); ?>" target="_blank"><i class="la la-print"></i></a>
                                    </td>

                                    <td>
                                        <?php echo e(date('d-M-Y',strtotime($grn->received_date))); ?>

                                    </td>

                                    <?php if($rkey == 0): ?>
                                    <td rowspan="<?php echo e($po->relGoodReceiveNote->count()); ?>">
                                        <?php echo e($po->relPurchaseOrderItems->sum('qty')); ?>

                                    </td>
                                    <?php endif; ?>
                                    <td><?php echo e($grn->relGoodsReceivedItems->sum('qty')); ?></td>
                                    <td class="text-center">
                                        <?php if($grn->received_status == 'partial'): ?>
                                            <a class="btn btn-warning btn-xs">Partial Received</a>
                                        <?php elseif($grn->received_status == 'full'): ?>
                                            <a class="btn btn-success btn-xs">Full Received</a>
                                        <?php else: ?>
                                            <a class="btn btn-dark btn-xs"><?php echo e(ucwords($grn->received_status)); ?></a>
                                        <?php endif; ?>
                                    </td>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('quality-ensure')): ?>
                                    <td class="text-center">
                                       <?php if($grn->relGoodsReceivedItems()->whereIn('quality_ensure',['pending'])->count() > 0): ?>
                                       <a href="<?php echo e(route('pms.quality.ensure.check',$grn->id)); ?>" title="Quality Ensure" class="btn btn-success btn-sm"><i class="las la-check-circle"> <?php echo e(__('Quality Ensure')); ?></i></a>
                                       <?php endif; ?>
                                   </td>
                                   <?php endif; ?>
                               </tr>
                               <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                               <?php endif; ?>
                               <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                               <?php endif; ?>
                           </tbody>
                       </table>
                       <div class="col-12 py-2">
                            <?php if(count($purchaseOrder)>0): ?>
                            <ul>
                                <?php echo e($purchaseOrder->links()); ?>

                            </ul>

                            <?php endif; ?>
                        </div>
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

        $('#searchGRNList').on('click', function () {

            let from_date=$('#from_date').val();
            let to_date=$('#to_date').val();
            let received_status=$('#received_status').val();

            const searchGRNList = () => {
                let container = document.querySelector('.searchPagination');
                let pageLink = container.querySelectorAll('.page-link');
                Array.from(pageLink).map((item, key) => {
                    item.addEventListener('click', (e)=>{
                        e.preventDefault();
                        let getHref = item.getAttribute('href');
                        showPreloader('block');
                        $.ajax({
                            type: 'post',
                            url: getHref,
                            dataType: "json",
                            data:{_token:'<?php echo csrf_token(); ?>',from_date:from_date,to_date:to_date,received_status:received_status},
                            success:function (data) {
                                if(data.result == 'success'){
                                    showPreloader('none');
                                    $('#viewResult').html(data.body);
                                    searchGRNList();
                                    showPODetails();
                                }else{
                                    showPreloader('none');
                                    notify(data.message,'error');

                                }
                            }
                        });
                    })

                });
            };
            
            if (from_date !='' || to_date !='' || received_status !='') {
                showPreloader('block');
                $.ajax({
                    type: 'post',
                    url: $(this).attr('data-src'),
                    dataType: "json",
                    data:{_token:'<?php echo csrf_token(); ?>',from_date:from_date,to_date:to_date,received_status:received_status},
                    success:function (data) {
                        if(data.result == 'success'){
                            showPreloader('none');
                            $('#viewResult').html(data.body);
                            searchGRNList();
                            showPODetails();
                        }else{
                            showPreloader('none');
                            $('#viewResult').html('<div class="col-md-12"><center>No Data Found!!</center></div>');

                        }
                    }
                });
                return false;
            }else{
                notify('Please enter data first !!','error');
            }
        });


        const showPODetails = () => {
            $('.showGateInPODetails').on('click', function () {

                var modalTitle= $(this).attr('data-title');
                $.ajax({
                    url: $(this).attr('data-src'),
                    type: 'get',
                    dataType: 'json',
                    data: '',
                })
                .done(function(response) {

                    if (response.result=='success') {
                        $('#POdetailsModel .modal-title').html(modalTitle);
                        $('#POdetailsModel').find('#body').html(response.body);
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
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/grn/index.blade.php ENDPATH**/ ?>