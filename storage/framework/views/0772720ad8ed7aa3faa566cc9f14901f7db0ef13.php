


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
                        <a href="javascript:void(0)" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add New Payment Term" id="addPaymentBtn"> <i class="las la-plus"></i>Add</a>
                    </li>
                </ul><!-- /.breadcrumb -->

            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body table-responsive">
                            <table  id="dataTable" class="table table-striped table-bordered table-head" border="1">
                                <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Payment Term</th>
                                    <th>Payment Percentage</th>
                                    <th>Day Duration</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php $__empty_1 = true; $__currentLoopData = $paymentTerms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$paymentTerm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($key+1); ?></td>
                                        <td><?php echo e($paymentTerm->term); ?></td>
                                        <td class="text-center"><?php echo e($paymentTerm->percentage); ?>%</td>
                                        <td class="text-center"><?php echo e($paymentTerm->days); ?></td>
                                        <td class="text-center"><?php echo e(ucwords($paymentTerm->type)); ?></td>
                                        <td><?php echo e($paymentTerm->status); ?></td>
                                        <td>
                                            <?php echo Form::open(array('route' => ['pms.payment-terms.destroy',$paymentTerm->id],'method'=>'DELETE','id'=>"deleteForm$paymentTerm->id")); ?>

                                            <a href="<?php echo e(route('pms.payment-terms.edit',$paymentTerm->id)); ?>" id="editModal_<?php echo e($paymentTerm->id); ?>" data-toggle="modal" onclick="editPaymentTerm(<?php echo e($paymentTerm->id); ?>)" class="btn btn-success btn-sm "><i class="la la-pencil-square"></i> </a>

                                            <button type="button" class="btn btn-danger btn-sm" onclick='return deleteConfirm("deleteForm<?php echo e($paymentTerm->id); ?>")'><i class="la la-trash"></i></button>
                                            <?php echo Form::close(); ?>

                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                                <?php endif; ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END WRAPPER CONTENT ------------------------------------------------------------------------->
    <!-- Modal ------------------------------------------------------------------------->
    <div class="modal fade bd-example-modal-md" id="paymentTermModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">Add Payment Term</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php echo Form::open(array('route' => 'pms.payment-terms.store','id'=>'paymentTermsForm','class'=>'form-horizontal','method'=>'POST','role'=>'form')); ?>


                <div class="modal-body">

                    <div class="form-group row">
                        <label for="term" class="control-label col-md-12">Payment Term:</label>
                        <div class="col-md-12">
                            <?php echo Form::text('term' ,old('term'),['id'=>'term', 'required'=>true,'class'=>'form-control rounded']); ?>

                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="percentage">Payment Percentage:</label>
                            <input id="percentage" required class="form-control rounded" name="percentage" type="number" min="1" max="100" value="<?php echo e(old('percentage', 100)); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="days">Day Duration:</label>
                            <input id="days" required class="form-control rounded" name="days" type="number" min="1" max="9999" value="<?php echo e(old('days', 1)); ?>">
                        </div>
                    </div>

                     
                        
                        
                            
                        
                    

                     <div class="form-group row">
                        <div class="col-md-6">
                            <label for="type">Type:</label>
                            <?php echo Form::select('type',$type,old('type'),['id'=>'type', 'required'=>true,'class'=>'form-control rounded','style'=>'width:100%']); ?>

                        </div>
                        <div class="col-md-6">
                            <label for="status">Status:</label>
                            <?php echo Form::select('status',$status,old('status'),['id'=>'status', 'required'=>true,'class'=>'form-control rounded','style'=>'width:100%']); ?>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary text-white rounded" id="requisitionTypeFormSubmit"><?php echo e(__('Save')); ?></button>
                </div>
                <?php echo Form::close();; ?>

            </div>
        </div>
    </div>
    <!-- END Modal ------------------------------------------------------------------------->

    <!--edit payment term-->
    <div class="modal fade bd-example-modal-md" id="editPaymentTermModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">Add Payment Term</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--edit payment term-->

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>

    <script>
       function editPaymentTerm(paymentTermId) {

           showPreloader('block');
           $('#editPaymentTermModal').load('<?php echo e(URL::to("pms/payment-terms")); ?>/'+paymentTermId);
           showPreloader('none');
           $('#editPaymentTermModal').modal('show');
       }

       $('#addPaymentBtn').on('click', function () {
           $('#paymentTermModal').modal('show');
//                form.setAttribute('data-type', 'post');
       });

    </script>

    <script>

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
            const tableContainer = document.getElementById('dataTable').querySelector('tbody');

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
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/payment-term/index.blade.php ENDPATH**/ ?>