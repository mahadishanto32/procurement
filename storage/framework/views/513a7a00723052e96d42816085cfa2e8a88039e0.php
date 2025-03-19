<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title'); ?> - MBM ERP</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo e(asset('images/mbm.ico')); ?> " />
    

    <!-- Styles -->
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet" media='screen,print'>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/all.css')); ?>" media='screen,print'>
    <?php echo $__env->yieldPushContent('css'); ?>
    
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom.css?v=1.3')); ?>" media='screen,print'>
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/responsive.css')); ?>" media='screen,print'>
     <!-- jQuery Confirm -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/js/jquery-confirm/jquery-confirm.min.css')); ?>" />
     <!-- toastr alert -->
    <link rel="stylesheet" href="<?php echo e(asset('notification_assets/css/toastr.min.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('plugins/air-datepicker/css/datepicker.min.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('plugins/summernote/summernote.min.css')); ?>" />
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">

    <?php echo $__env->yieldContent('page-css'); ?>
</head><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/my_project/backend/layouts/head.blade.php ENDPATH**/ ?>