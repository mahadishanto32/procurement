<script !src="">
    toastr.options = { 
        "progressBar": false,
        "showDuration": "1000",
        "hideDuration": "1000",
        "timeOut": "1000",
        "extendedTimeOut": "0",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

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
<?php /**PATH /home/sites/34b/a/a55a29c214/procurement.zbridea.com/resources/views/pms/backend/layouts/toster-script.blade.php ENDPATH**/ ?>