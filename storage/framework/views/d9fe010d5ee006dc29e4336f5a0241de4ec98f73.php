<div class="col-lg-12">
    <div class="row">
        <div class="col-lg-9">
            <div class="row">
                <div class="col-md-8">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                            <div class="iq-header-title">
                                <h4 class="card-title text-primary border-left-heading">STORE MANAGE STATS</h4>
                            </div>
                        </div>
                        <div class="iq-card-body p-0">
                            <canvas class="bar-charts" id="store-manage-chart" data-data="<?php echo e(implode(',', array_values($storeData['store-manage']))); ?>" data-labels="<?php echo e(implode(',', array_map(function($value){
                                return ucwords(str_replace('-', ' ', $value));
                            }, array_keys($storeData['store-manage'])))); ?>" data-legend-position="top" data-title-text="Total Count" width="200" height="105"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                            <div class="iq-header-title">
                                <h4 class="card-title text-primary border-left-heading">GRN STATS</h4>
                            </div>
                        </div>
                        <div class="iq-card-body p-0">
                            <canvas class="bar-charts" id="grn-chart" data-data="<?php echo e(implode(',', array_values($storeData['grn']))); ?>" data-labels="<?php echo e(implode(',', array_map(function($value){
                                return ucwords(str_replace('-', ' ', $value));
                            }, array_keys($storeData['grn'])))); ?>" data-legend-position="top" data-title-text="Total Count" width="200" height="225"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="iq-card">
                <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                    <div class="iq-header-title">
                        <h4 class="card-title text-primary border-left-heading">INVENTORY IN/OUT STATS</h4>
                    </div>
                </div>
                <div class="iq-card-body p-0">
                    <canvas class="charts" data-data="<?php echo e(inventoryStatus('in').','.inventoryStatus('out')); ?>" data-labels="In,Out" data-chart="pie" data-legend-position="top" data-title-text="All Warehouses" width="200" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="col-lg-12">
    <div class="iq-card">
        <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
            <div class="iq-header-title">
                <h4 class="card-title text-primary border-left-heading">INVENTORY STATS</h4>
            </div>
        </div>
        <div class="iq-card-body p-0">
            <div class="hr-section">
                <section id="features">
                    <div class="container-fluid p-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 d-flex justify-content-center pl-0">
                                    <div class="project-card" style="height: auto !important">
                                        <div class="project-card-header">
                                            <h5><i class="las la-store-alt"></i>&nbsp;All Warehouses</h5>
                                        </div>
                                        <div class="project-card-body pb-3">
                                            <canvas class="charts" data-data="<?php echo e(inventoryStatus('in').','.inventoryStatus('out')); ?>" data-labels="In,Out" data-chart="pie" data-legend-position="top" data-title-text="" width="200" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    $warehouses = \App\Models\PmsModels\Warehouses::has('inventoryLogs')->get();
                                ?>
                                <?php if(isset($warehouses[0])): ?>
                                <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-3 d-flex justify-content-center">
                                        <div class="project-card" style="height: auto !important">
                                            <div class="project-card-header">
                                                <h5><i class="las la-store-alt"></i>&nbsp;<?php echo e($warehouse->name); ?></h5>
                                            </div>
                                            <div class="project-card-body pb-3">
                                                <canvas class="charts" data-data="<?php echo e(inventoryStatus('in', $warehouse->id).','.inventoryStatus('out', $warehouse->id)); ?>" data-labels="In,Out" data-chart="pie" data-legend-position="top" data-title-text="" width="200" height="200"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/dashboard-partials/store.blade.php ENDPATH**/ ?>