

<?php $__env->startSection('title', config('app.name', 'laravel'). ' | '.$title); ?>

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
              <li class="active"><?php echo e(__($title)); ?> List</li>
              
          </ul>
      </div>

      <div class="page-content">
        <div class="">
            <div class="panel panel-info">
                <div class="panel-body">
                    <form action="<?php echo e(route('pms.requisition.view.all.notification')); ?>" method="get" accept-charset="utf-8">
                        <div class="row">

                            <div class="col-md-6 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="search_text">Enter Search Text</label></p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="search_text" id="search_text" class="form-control" placeholder="Search Notification Here..." value="<?php echo e(request()->has('search_text') ? request()->get('search_text') : ''); ?>"/>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="searchDeliveredRequisitonBtn"></label></p>
                                <div class="input-group input-group-md">
                                    <button type="submit" class="btn btn-success rounded mt-8"><i class="las la-search"></i>&nbsp;Search</button>
                                </div>
                            </div>

                        </div>  
                    </form>                  
                </div>

                <div class="panel-body">
                    <table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="<?php echo e($title); ?>" border="0" id="dataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo e(__('Unit')); ?></th>
                                <th><?php echo e(__('Department')); ?></th>
                                <th>Requisition Date</th>
                                <th>Notification Date</th>
                                <th>Requisition RefNo</th>
                                <th>Category</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th class="text-center">Option</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($notification)): ?>
                            <?php $__currentLoopData = $notification; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e(($notification->currentpage()-1) * $notification->perpage() + $key + 1); ?></td>
                                <td>
                                    <?php echo e(isset($values->relUser->employee->unit->hr_unit_short_name)?$values->relUser->employee->unit->hr_unit_short_name:''); ?>

                                </td>
                                <td>
                                    <?php echo e(isset($values->relUser->employee->department->hr_department_name)?$values->relUser->employee->department->hr_department_name:''); ?>

                                </td>
                                <td><?php echo e(isset($values->relRequisitionItem->requisition->requisition_date)?date('d-m-Y',strtotime($values->relRequisitionItem->requisition->requisition_date)):''); ?></td>

                                <td><?php echo e(date('d-m-Y',strtotime($values->created_at))); ?></td>

                                <td>
                                    <?php if(isset($values->relRequisitionItem->requisition_id)): ?>
                                    <a href="javascript:void(0)"  data-src="<?php echo e(route('pms.requisition.list.view.show',$values->relRequisitionItem->requisition_id)); ?>" class="btn btn-link requisition m-1 rounded showRequistionDetails"><?php echo e($values->relRequisitionItem->requisition->reference_no); ?></a>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e(isset($values->relRequisitionItem->product->category->name)?$values->relRequisitionItem->product->category->name:''); ?></td>
                                <td>
                                    <?php if(isset($values->relRequisitionItem->product->name)): ?>
                                    <?php echo e($values->relRequisitionItem->product->name); ?> (<?php echo e(getProductAttributes($values->relRequisitionItem->product->id)); ?>)
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e(isset($values->relRequisitionItem->qty)?$values->relRequisitionItem->qty:''); ?></td>
                                <td><?php echo $values->messages; ?></td>
                                <td id="type<?php echo e($values->id); ?>">
                                    <?php if($values->type=='unread'): ?>
                                    <span class="btn btn-sm btn-warning">Unread</span>
                                    <?php else: ?>
                                    <span class="btn btn-sm btn-success">Read</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center action" id="action<?php echo e($values->id); ?>">
                                    <?php if($values->type=='unread'): ?>
                                    <div class="btn-group">
                                        <button class="btn dropdown-toggle" data-toggle="dropdown">
                                            <span id="statusName<?php echo e($values->id); ?>">
                                                <?php echo e(__('Option')); ?>

                                            </span>
                                        </button>
                                        <ul class="dropdown-menu">
                                         <li><a href="javascript:void(0)" class="markAsRead" data-id="<?php echo e($values->id); ?>" title="Mark As Read"><i class="la la-check"></i> <?php echo e(__('Mark As Read')); ?></a>
                                         </li>
                                     </ul>
                                 </div>
                                 <?php else: ?>
                                 Already Read
                                 <?php endif; ?>
                             </td>
                         </tr>
                         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                         <?php endif; ?>
                     </tbody>

                 </table>


                 <div class="row">
                    <div class="col-md-12">
                        <div class="la-1x pull-right">
                            <?php if(count($notification)>0): ?>
                            <ul>
                                <?php echo e($notification->links()); ?>

                            </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
<script>

    $('.markAsRead').on('click', function () {

        let id = $(this).attr('data-id');
        
        $.ajax({
            url: "<?php echo e(url('pms/requisition/mark-as-read')); ?>",
            type: 'POST',
            dataType: 'json',
            data: {_token: "<?php echo e(csrf_token()); ?>", id:id},
        })
        .done(function(response) {
            if(response.result=='success'){
                $('#type'+id).html('<span class="btn btn-sm btn-success">Read</span>');
                $('#action'+id).html('<span class="btn btn-sm btn-success">Read</span>');
                notify(response.message,response.result);
            }
        })
        .fail(function(response){
            notify('Something went wrong!','error');
        });
        return false;
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/requisitions/notification-list.blade.php ENDPATH**/ ?>