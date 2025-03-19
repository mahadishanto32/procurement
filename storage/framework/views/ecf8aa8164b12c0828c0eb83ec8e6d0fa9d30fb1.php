 

<div class="col-lg-12">
    <div class="iq-card">
        <div class="iq-card-body p-0">
            <div class="hr-section">
                <section id="features">
                    <div class="container-fluid p-0">
                        <div class="row">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('requisition-list')): ?>
                            <div class="col-md-<?php echo e((Auth::user()->hasPermissionTo('project-action'))?3:4); ?>">
                                <div class="project-card" style="height: auto !important">
                                    <div class="project-card-header">
                                        <h5 class="mb-0">
                                            <i class="las la-receipt"></i>&nbsp;&nbsp;Requisitions
                                        </h5>
                                    </div>
                                    <div class="project-card-body pb-3">
                                        <canvas id="requisition-progress" class="charts" data-data="<?php echo e(implode(',', array_values($userData['requisitions']))); ?>" data-labels="Draft,Pending,Approved,Processing,Delivered,Received" data-chart="pie" data-legend-position="top" data-title-text="" width="300" height="300"></canvas>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                           

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('requisition-delivered-list')): ?>
                            <div class="col-md-<?php echo e((Auth::user()->hasPermissionTo('project-action'))?3:4); ?>">
                                <div class="project-card" style="height: auto !important">
                                    <div class="project-card-header">
                                        <h5 class="mb-0">
                                            <i class="las la-truck-loading"></i>&nbsp;&nbsp;Delivered Requisitions
                                        </h5>
                                    </div>
                                    <div class="project-card-body pb-3">
                                        <canvas id="delivered-requisitions" class="charts" data-data="<?php echo e(implode(',', array_values($userData['delivered-requisitions']))); ?>" data-labels="Pending,Acknowledge,Delivered" data-chart="pie" data-legend-position="top" data-title-text="" width="300" height="300"></canvas>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                             <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('notification-list')): ?>
                            <div class="col-md-<?php echo e((Auth::user()->hasPermissionTo('project-action'))?3:4); ?>">
                                <div class="project-card" style="height: auto !important">
                                    <div class="project-card-header">
                                        <h5 class="mb-0">
                                            <i class="las la-bell"></i>&nbsp;&nbsp;Notifications
                                        </h5>
                                    </div>
                                    <div class="project-card-body pb-3">
                                        <canvas id="notifications" class="charts" data-data="<?php echo e(implode(',', array_values($userData['notifications']))); ?>" data-labels="Read,Unread" data-chart="pie" data-legend-position="top" data-title-text="" width="300" height="300"></canvas>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('project-action')): ?>
                            <div class="col-md-3">
                                <div class="project-card" style="height: auto !important">
                                    <div class="project-card-header">
                                        <h5 class="mb-0">
                                            <i class="las la-project-diagram"></i>&nbsp;&nbsp;Projects (%)
                                        </h5>
                                    </div>
                                    <div class="project-card-body pb-3">
                                        <canvas id="projects" class="charts" data-data="<?php echo e(implode(',', array_values($projectData['progresses']))); ?>" data-labels="<?php echo e(implode(',', array_values($projectData['names']))); ?>" data-chart="pie" data-legend-position="top" data-title-text="" width="300" height="300"></canvas>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>


<?php /**PATH /home/sites/34b/a/a55a29c214/procurement.zbridea.com/resources/views/pms/backend/pages/dashboard-partials/users.blade.php ENDPATH**/ ?>