<script !src="">
    <?php if(Session::has('message')): ?>
    var type = "<?php echo e(Session::get('alert-type', 'info')); ?>";
    switch (type) {
        case 'info':
        toastr.info("<?php echo e(Session::get('message')); ?>");
        break;

        case 'warning':
        toastr.warning("<?php echo e(Session::get('message')); ?>");
        break;

        case 'success':
        toastr.success("<?php echo e(Session::get('message')); ?>");
        break;

        case 'error':
        toastr.error("<?php echo e(Session::get('message')); ?>");
        break;
    }
    <?php elseif(count($errors) > 0): ?>
    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    toastr.error("<?php echo e($error); ?>");
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
    
    
</script>
<?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/my_project/backend/layouts/toster-script.blade.php ENDPATH**/ ?>