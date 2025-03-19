
<?php $__env->startSection('title', 'User Dashboard'); ?>
<?php $__env->startSection('main-content'); ?>
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">ESS</a>
                </li>  
                <li class="active">Change Password</li>
            </ul><!-- /.breadcrumb -->

        </div>

        <?php echo $__env->make('inc/message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="panel h-min-400"> 
            <div class="panel-body"> 
                <div class="row justify-content-center">
                    <div class="col-sm-3">
                        <?php echo Form::open(['url'=>['user/change-password'], 'class'=>'form-horizontal']); ?>

                        <div class="form-group">
                            <label for="password" > Password </label>
                            <input type="password" id="password" name="password" placeholder="Password"  value="<?php echo e(old('password')); ?>" class="form-control" data-validation-length="min6" data-validation="required length"/>
                        </div> 

                        <div class="form-group">
                            <label for="password_confirmation" >Confirm Password </label>
                            <input type="password" id="password_confirmation" name="password_confirmation" value="<?php echo e(old('password_confirmation')); ?>" placeholder="Confirm Password" class="form-control" />
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">
                                <i class="ace-icon fa fa-check bigger-110"></i> Update
                            </button>
                        </div>

                        <?php echo Form::close(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  
<?php $__env->stopSection(); ?>
<?php echo $__env->make('user.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/user/change-password.blade.php ENDPATH**/ ?>