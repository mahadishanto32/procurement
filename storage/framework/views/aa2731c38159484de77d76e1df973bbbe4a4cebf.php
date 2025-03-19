


<?php $__env->startSection('title', config('app.name', 'laravel'). ' | '.$title); ?>

<?php $__env->startSection('page-css'); ?>
<style type="text/css">
    .modal-backdrop{
        position: relative !important;
    }

</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<!-- WRAPPER CONTENT ----------------------------------------------------------------------------->
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Home</a>
                </li>
                <li>
                    <a href="#">PMS</a>
                </li>
                <li class="active"><?php echo e(__($title)); ?> List</li>
                <li class="top-nav-btn">
                    <a href="<?php echo e(route('pms.admin.users.create')); ?>" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add New User"> <i class="las la-plus"></i>Add New User</a>

                    <a href="<?php echo e(route('pms.admin.users.deleted')); ?>" class="btn btn-sm btn-danger text-white" data-toggle="tooltip" title="Active Users"> <i class="las la-ban"></i>View Deleted Users</a>
                </li>
            </ul><!-- /.breadcrumb -->

        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <div class="panel-body table-responsive">

                        <table id="dataTable" class="table table-striped table-bordered table-head datatable-exportable" data-table-name="<?php echo e($title); ?>" border="1">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Designation</th>
                                    <th>Associate Id</th>
                                    <th>Unit</th>
                                    <th>Department</th>
                                    <th>Role</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($users[0])): ?>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($key+1); ?></td>
                                    <td><?php echo e($user->name); ?></td>
                                    <td><?php echo e($user->email); ?></td>
                                    <td>
                                        <?php echo e(($user->employee ? $user->employee->designation->hr_designation_name : '')); ?>

                                    </td>
                                    <td><?php echo e($user->associate_id); ?></td>
                                    <td><?php echo e(($user->employee ? $user->employee->unit->hr_unit_name : '')); ?></td>
                                    <td>
                                        <?php echo e(isset($user->employee->department->hr_department_name) ? $user->employee->department->hr_department_name : ''); ?>

                                    </td>
                                    
                                    <td><?php echo e(implode(', ', $user->getRoleNames()->toArray())); ?></td>
                                    <td><?php echo e(date('d-m-Y',strtotime($user->created_at))); ?></td>
                                    <td>
                                     <?php echo Form::open(array('route'=> ['pms.admin.users.destroy', $user->id],'method'=>'DELETE','class'=>'deleteForm','id'=>"deleteForm".$user->id)); ?>

                                     <?php echo e(Form::hidden('id', $user->id)); ?>

                                     <a href="javascript:void(0)" onclick="return showUserDetails(<?php echo e($user->id); ?>)" class="btn btn-info btn-xs mb-2"><i class="la la-eye" title="Click to view details"></i></a>

                                     <a href="<?php echo e(route('pms.admin.users.edit', $user->id)); ?>" class="btn btn-warning btn-xs mb-2"><i class="la la-pencil-square" title="Click to Edit"></i></a>
                                     <button type="button" onclick='return deleteConfirm("deleteForm<?php echo e($user->id); ?>");' class="btn btn-danger btn-xs mb-2">
                                         <i class="la la-trash"></i></button>
                                         <?php echo Form::close(); ?>

                                     </td>
                                 </tr>
                                 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                 <?php endif; ?>
                             </tbody>
                         </table>
                         <div class="row">
                            <div class="col-md-12">
                                <div class="la-1x pull-right">
                                    <?php if(count($users)>0): ?>
                                    <ul>
                                        <?php echo e($users->links()); ?>

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
<!-- END WRAPPER CONTENT ------------------------------------------------------------------------->
<!-- Modal ------------------------------------------------------------------------->
<div class="modal fade bd-example-modal-md" id="showUserDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center">Use Details Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" id="dataBody">

            </div>
        </div>
    </div>
</div>
<!-- END Modal ------------------------------------------------------------------------->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>

<script>

    function showUserDetails(userId) {

        $('#dataBody').empty().load('<?php echo e(URL::to(Request()->route()->getPrefix()."/users")); ?>/'+userId);

        $('#showUserDetailsModal').modal('show');
    }

    function deleteConfirm(id){
        swal({
            title: "<?php echo e(__('Are you sure?')); ?>",
            text: "You won't be able to revert this!",
            icon: "warning",
            dangerMode: true,
            buttons: {
                cancel: true,
                confirm: {
                    text: "Yes, delete it!",
                    value: true,
                    visible: true,
                    closeModal: true
                },
            },
        }).then((result) => {
            if (result) {
                $("#"+id).submit();
            }
        })
    }
</script>

<script>
    (function ($) {
        "use script";
        $('[data-toggle="tooltip"]').tooltip();
        const form = document.getElementById('permissionForm');

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
                if(value)form.reset();
            });
        };

    })(jQuery)
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/users/index.blade.php ENDPATH**/ ?>