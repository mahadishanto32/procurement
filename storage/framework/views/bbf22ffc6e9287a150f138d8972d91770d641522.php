<?php $__env->startSection('title', config('app.name', 'laravel'). ' | '.$title); ?>

<?php $__env->startSection('page-css'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<!-- WRAPPER CONTENT ----------------------------------------------------------------------------->
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
              <li class="top-nav-btn">
                <a href="<?php echo e(route('pms.requisition.requisition.create')); ?>" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add New Requisition" id="addProductBtn"> <i class="las la-plus"></i>Add</a>
            </li>
        </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="">
            <div class="panel panel-info">
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="<?php echo e($title); ?>" border="0" id="dataTable">
                        <thead>
                            <tr>
                                <th width="2%"><?php echo e(__('SL No.')); ?></th>
                                <th><?php echo e(__('Unit')); ?></th>
                                <th><?php echo e(__('Department')); ?></th>
                                <th><?php echo e(__('Date')); ?></th>
                                <th><?php echo e(__('Ref: No')); ?></th>
                                <th><?php echo e(__('Requisition By')); ?></th>
                                <th><?php echo e(__('Qty')); ?></th>
                                <th><?php echo e(__('Status')); ?></th>
                                <th class="text-center"><?php echo e(__('Option')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $requisitions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $requisition): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td width="5%"><?php echo e(($requisitions->currentpage()-1) * $requisitions->perpage() + $key + 1); ?></td>
                                <td>
                                    <?php echo e($requisition->relUsersList->employee->unit->hr_unit_short_name?$requisition->relUsersList->employee->unit->hr_unit_short_name:''); ?>

                                </td>
                                <td>
                                    <?php echo e($requisition->relUsersList->employee->department->hr_department_name?$requisition->relUsersList->employee->department->hr_department_name:''); ?>

                                </td>
                                <td>
                                    <?php echo e(date('d-m-Y',strtotime($requisition->requisition_date))); ?>

                                </td>

                                <td><a href="javascript:void(0)" data-src="<?php echo e(route('pms.requisition.list.view.show',$requisition->id)); ?>" class="btn btn-link requisition m-1 rounded showRequistionDetails"><?php echo e($requisition->reference_no); ?></a></td>
                                <td><?php echo e($requisition->relUsersList->name); ?></td>
                                <td><?php echo e($requisition->items->sum('qty')); ?></td>
                                <td id="status<?php echo e($requisition->id); ?>">
                                    <?php if($requisition->status==0): ?>
                                        <span class="btn btn-sm btn-warning">Pending</span>
                                    <?php elseif($requisition->status==1): ?>
                                        <span class="btn btn-sm btn-success">Approved</span>
                                    <?php elseif($requisition->status==2): ?>
                                        <span class="btn btn-sm btn-danger">Halt</span>
                                    <?php elseif($requisition->status==3): ?>
                                        <span class="btn btn-sm btn-warning">Draft</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center action">
                                    <div class="btn-group">
                                        <button class="btn dropdown-toggle" data-toggle="dropdown">
                                            <span id="statusName<?php echo e($requisition->id); ?>">
                                                <?php echo e(__('Option')); ?>

                                            </span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="javascript:void(0)" title="Tracking Requisition" class="trackingRequistionStatus" data-id="<?php echo e($requisition->id); ?>"><i class="la la-map"></i> <?php echo e(__('Track Your Requisition')); ?></a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" data-role="delete" data-src="<?php echo e(route('pms.requisition.requisition.destroy', $requisition->id)); ?>" class="text-danger deleteBtn"><i class="las la-trash"></i>&nbsp;Delete Requisition</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>

                    </table>
                  

                    <div class="row">
                        <div class="col-md-12">
                            <div class="la-1x pull-right">
                                <?php if(count($requisitions)>0): ?>
                                <ul>
                                    <?php echo e($requisitions->links()); ?>

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
    (function ($) {
        "use script";

        const showAlert = (status, error) => {
            swal({
                icon: status,
                text: error,
                dangerMode: true,
                buttons: {
                    cancel: false,
                    confirm: {
                        text: "OK",
                        value: true,
                        visible: true,
                        closeModal: true
                    },
                },
            }).then((value) => {
                // if(value) form.reset();
            });
        };

        $('.deleteBtn').on('click', function () {
            var element = $(this);
            swal({
                title: "<?php echo e(__('Are you sure?')); ?>",
                text: "<?php echo e(__('Once you delete, You can not recover this data and related files.')); ?>",
                icon: "warning",
                dangerMode: true,
                buttons: {
                    cancel: true,
                    confirm: {
                        text: "Delete",
                        value: true,
                        visible: true,
                        closeModal: true
                    },
                },
            }).then((value) => {
                if(value){
                    $.ajax({
                        type: 'DELETE',
                        url: element.attr('data-src'),
                        success:function (response) {
                            if(response.success){
                                element.parent().parent().parent().parent().parent().remove();
                                swal({
                                    icon: 'success',
                                    text: 'Data deleted successfully',
                                    button: false
                                });
                                setTimeout(()=>{
                                    swal.close();
                                }, 1500);
                            }else{
                                showAlert('error', response.message);
                                return;
                            }
                        },
                    });
                }
            });
        })
    })(jQuery)
    
    $('.trackingRequistionStatus').on('click', function () {
        let id = $(this).attr('data-id');
        $.ajax({
            url: "<?php echo e(url('pms/requisition/tracking-show')); ?>",
            type: 'POST',
            dataType: 'json',
            data: {_token: "<?php echo e(csrf_token()); ?>", id:id},
        })
        .done(function(response) {
            if(response.result=='success'){
                $('#requisitionDetailModal').find('.modal-title').html(`Requisition Tracking`);
                $('#requisitionDetailModal').find('#tableData').html(response.body);
                $('#requisitionDetailModal').modal('show');
            }else{
                notify(response.message,response.result);
            }
        })
        .fail(function(response){
            notify('Something went wrong!','error');
        });
        return false;
    });

    $('.sendRequisition').on('click', function () {
        let sendButton=$(this).parent('li');
        let id = $(this).attr('data-id');
        let status = $(this).attr('data-status');

        let texStatus='Send';
        let textContent='Would you like to send this requisition to your department head?';

        swal({
            title: "<?php echo e(__('Are you sure?')); ?>",
            text: textContent,
            icon: "warning",
            dangerMode: true,
            buttons: {
                cancel: true,
                confirm: {
                    text: texStatus,
                    value: true,
                    visible: true,
                    closeModal: true
                },
            },
        }).then((value) => {
            if(value){
                $.ajax({
                    url: "<?php echo e(url('pms/requisition/approved-status')); ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: {_token: "<?php echo e(csrf_token()); ?>", id:id, status:status},
                })
                .done(function(response) {
                    if(response.success){
                        $('#statusName'+id).html(response.new_text);
                        $('#status'+id).html('<span class="btn btn-sm btn-warning">'+response.new_text+'</span>');
                        notify(response.message,'success')
                        sendButton.remove();
                    }else{
                        notify(response.message,'error');
                    }
                })
                .fail(function(response){
                    notify('Something went wrong!','error');
                });
                return false;
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/sites/34b/a/a55a29c214/procurement.zbridea.com/resources/views/pms/backend/pages/requisitions/halt-index.blade.php ENDPATH**/ ?>