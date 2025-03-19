<div class="col-lg-12">
    <div class="iq-card">
        
        <div class="iq-card-body p-0">
            <div class="hr-section">
                <section id="features">
                    <div class="container-fluid p-0">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="iq-card">
                                    <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                                        <div class="iq-header-title">
                                            <h4 class="card-title text-primary border-left-heading">GATE-IN STATS</h4>
                                        </div>
                                    </div>
                                    <div class="iq-card-body p-0">
                                        <canvas class="bar-charts" id="gate-in-list_-chart" data-data="<?php echo e(implode(',', array_values($gateQualityControllerData['gate-in']))); ?>" data-labels="<?php echo e(implode(',', array_keys($gateQualityControllerData['gate-in']))); ?>" data-legend-position="top" data-title-text="Total Count" width="200" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="iq-card">
                                    <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                                        <div class="iq-header-title">
                                            <h4 class="card-title text-primary border-left-heading">QUALITY ENSURE APPROVED STATS</h4>
                                        </div>
                                    </div>
                                    <div class="iq-card-body p-0">
                                        <canvas class="bar-charts" id="approved-chart" data-data="<?php echo e(implode(',', array_values($gateQualityControllerData['approved']))); ?>" data-labels="<?php echo e(implode(',', array_keys($gateQualityControllerData['approved']))); ?>" data-legend-position="top" data-title-text="Total Count" width="200" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="iq-card">
                                    <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                                        <div class="iq-header-title">
                                            <h4 class="card-title text-primary border-left-heading">QUALITY ENSURE RETURN STATS</h4>
                                        </div>
                                    </div>
                                    <div class="iq-card-body p-0">
                                        <canvas class="bar-charts" id="returned-chart" data-data="<?php echo e(implode(',', array_values($gateQualityControllerData['returned']))); ?>" data-labels="<?php echo e(implode(',', array_keys($gateQualityControllerData['returned']))); ?>" data-legend-position="top" data-title-text="Total Count" width="200" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="iq-card">
                                    <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                                        <div class="iq-header-title">
                                            <h4 class="card-title text-primary border-left-heading">QUALITY ENSURE RETRUN REPLACE STATS</h4>
                                        </div>
                                    </div>
                                    <div class="iq-card-body p-0">
                                        <canvas class="bar-charts" id="return-changed-chart" data-data="<?php echo e(implode(',', array_values($gateQualityControllerData['return-changed']))); ?>" data-labels="<?php echo e(implode(',', array_keys($gateQualityControllerData['return-changed']))); ?>" data-legend-position="top" data-title-text="Total Count" width="200" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/dashboard-partials/quality-controller.blade.php ENDPATH**/ ?>