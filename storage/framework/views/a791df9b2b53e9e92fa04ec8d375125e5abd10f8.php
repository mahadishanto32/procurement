 <?php
 $userData = userData();
 $projectData = projectData();
 $storeData = storeData();
 $purchaseStats = purchaseStats();
 $gateManagerData = gateManagerData();
 $gateQualityControllerData = gateQualityControllerData();
 ?>

 <div class="col-lg-12 pl-0">
    <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
        <div class="iq-card-body pb-0">
            <div class="row"> 
                <div class="col-sm-12">
                    <div class="iq-card">
                        <div class="iq-card-body bg-primary rounded pt-2 pb-2 pr-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="mb-0">Welcome to MBM PMS, Stay connected!</p>
                                <div class="rounded iq-card-icon bg-white">
                                    <img src="<?php echo e(asset('assets/images/page-img/37.png')); ?>" class="img-fluid" alt="icon">
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
                

                <?php if(!Auth::user()->hasRole('Super Admin') ): ?>
                <?php echo $__env->make('pms.backend.pages.dashboard-partials.users', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>

                <?php if(Auth::user()->hasRole('Department-Head') || Auth::user()->hasRole('Super Admin')): ?>
                <?php echo $__env->make('pms.backend.pages.dashboard-partials.department-head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>

                <?php if(Auth::user()->hasRole('Store-Manager')|| Auth::user()->hasRole('Store-Department') || Auth::user()->hasRole('Super Admin')): ?>
                <?php echo $__env->make('pms.backend.pages.dashboard-partials.store', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>

                <?php if(Auth::user()->hasRole('Purchase-Department') || Auth::user()->hasRole('Super Admin')): ?>
                <?php echo $__env->make('pms.backend.pages.dashboard-partials.purchase', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>

                <?php if(Auth::user()->hasRole('Management') || Auth::user()->hasRole('Super Admin')): ?>
                <?php echo $__env->make('pms.backend.pages.dashboard-partials.quotation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('Gate Permission') || Auth::user()->hasRole('Super Admin')): ?>
                <?php echo $__env->make('pms.backend.pages.dashboard-partials.gate-manager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>

                <?php if(Auth::user()->hasRole('Quality-Ensure') || Auth::user()->hasRole('Super Admin')): ?>
                <?php echo $__env->make('pms.backend.pages.dashboard-partials.quality-controller', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>

                <?php if(Auth::user()->hasRole('Billing') || Auth::user()->hasRole('Super Admin')): ?>
                <?php echo $__env->make('pms.backend.pages.dashboard-partials.billing', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>

                <?php if(Auth::user()->hasRole('Audit') || Auth::user()->hasRole('Super Admin')): ?>
                <?php echo $__env->make('pms.backend.pages.dashboard-partials.audit', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('Accounts') || Auth::user()->hasRole('Super Admin') ): ?>
                <?php echo $__env->make('pms.backend.pages.dashboard-partials.accounts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>

                <?php echo $__env->make('pms.backend.pages.dashboard-partials.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </div>
</div><?php /**PATH /home/sites/34b/a/a55a29c214/procurement.zbridea.com/resources/views/pms/backend/pages/dashboard-partials/admin-link.blade.php ENDPATH**/ ?>