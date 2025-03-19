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
    

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/b-1.6.3/b-flash-1.6.3/b-html5-1.6.3/b-print-1.6.3/fc-3.3.1/fh-3.1.7/r-2.2.5/sc-2.0.2/datatables.min.css"/>

    <?php echo $__env->yieldContent('page-css'); ?>

    <style type="text/css" media="screen">
        .dataTables_wrapper .dt-buttons {
            padding-left: 0% !important;
        }

        .dataTables_scroll{
            margin-bottom: 10px;
        }

        .pagination{
            margin-bottom: 0px !important;
        }
    </style>
</head><?php /**PATH /home/sites/34b/a/a55a29c214/procurement.zbridea.com/resources/views/pms/backend/layouts/head.blade.php ENDPATH**/ ?>