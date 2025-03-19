<!DOCTYPE html>
<html lang="en">
<?php echo $__env->make('pms.backend.layouts.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<body>
    <!------------------------------------------------------------------------------------------------>
    <?php echo $__env->make('pms.backend.layouts.pre-loader', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- WRAPPER ------------------------------------------------------------------------------------->
    <div id="app">
        <!-- Wrapper Start -->
        <div class="wrapper">
            <!------------------------------------------------------------------------------------------------>
            <?php echo $__env->make('pms.backend.menus.left-menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <!------------------------------------------------------------------------------------------------>
            <!-- Page Content  -->
            <div id="content-page" class="content-page">
                <?php echo $__env->make('pms.backend.menus.header-menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <!------------------------------------------------------------------------------------------------>
                <main class="">
                  <div id="main-body" class="container-fluid">
                    <?php echo $__env->yieldContent('main-content'); ?>
                  </div>
                </main>
                <!------------------------------------------------------------------------------------------------>
                <?php echo $__env->make('pms.backend.layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div class="app-loader">
                <i class="fa fa-spinner fa-spin"></i>
            </div>
        </div>
        <!-- END WRAPPER --------------------------------------------------------------------------------->
    </div>
    <!------------------------------------------------------------------------------------------------>
    <?php echo $__env->make('pms.backend.layouts.script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <?php echo $__env->yieldContent('page-script'); ?>
    <?php echo $__env->make('pms.backend.layouts.toster-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('pms.backend.layouts.tools', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>

</html>
<?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/layouts/master-layout.blade.php ENDPATH**/ ?>