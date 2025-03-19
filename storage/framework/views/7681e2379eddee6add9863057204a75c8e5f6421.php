

<div class="col-lg-12">
    <div class="iq-card">
        <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
            <div class="iq-header-title">
                <h4 class="card-title text-primary border-left-heading">AUDIT MANAGEMENT | TOP (12)</h4>
            </div>
        </div>
        <div class="iq-card-body p-0">

            <div class="container-fluid p-0">
                <?php
                $topSuppliers = topSuppliers(12);
                $auditStat = auditStat();
                ?>
                <div class="row">
                   <div class="col-md-3">
                    <div class="project-card" style="height: auto !important">
                        <div class="project-card-header">
                            <h5 class="mb-0">
                                <i class="las la-truck-loading"></i>&nbsp;&nbsp;Billing Attachment
                            </h5>
                        </div>
                        <div class="project-card-body">
                            <canvas id="billing-attachment-list" class="charts" data-data="<?php echo e(implode(',', array_values($auditStat['purchase-orders']))); ?>" data-labels="Total,Pending,Approved" data-chart="doughnut" data-legend-position="top" data-title-text="" width="300" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <?php if(isset($topSuppliers[0])): ?>
                <?php $__currentLoopData = $topSuppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                $purchaseOrders = \App\Models\PmsModels\Purchase\PurchaseOrder::where('is_send','yes')
                ->whereHas('relQuotation', function ($query) use($supplier){
                    return $query->where('supplier_id', $supplier->id);
                })
                ->whereHas('relGoodReceiveNote', function ($query){
                    $query->whereRaw('purchase_orders.id=goods_received_notes.purchase_order_id');
                })->pluck('id')->toArray();

                $po = \App\Models\PmsModels\Purchase\PurchaseOrder::whereIn('id', $purchaseOrders)->sum('gross_price');
                $grn = \App\Models\PmsModels\Grn\GoodsReceivedItemStockIn::whereIn('purchase_order_id', $purchaseOrders)->where('is_grn_complete','yes')->sum('total_amount');
                $bill = \App\Models\PmsModels\Purchase\PurchaseOrderAttachment::whereIn('purchase_order_id', $purchaseOrders)->sum('bill_amount');
                ?>
                <div class="col-md-3 pr-0">
                    <div class="project-card" style="height: auto !important">
                        <div class="project-card-header">
                            <h6 class="mb-0">
                                <i class="la la-user-secret"></i>&nbsp;&nbsp;<?php echo e($supplier->name); ?>

                            </h6>
                        </div>
                        <div class="project-card-body pb-3">
                            <canvas class="bar-charts" id="supplier-<?php echo e($supplier->id); ?>-billing_-chart" data-data="<?php echo e($po.','.$grn.','.$bill); ?>" data-labels="PO,GRN,BILL" data-legend-position="top" data-title-text="Amount" width="200" height="175"></canvas>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div>

<?php
$from = date('Y-m-d', strtotime('-30 days'));
$to = date('Y-m-d');
$billingData = billingData($from, $to);
?>
<div class="col-lg-12">
    <div class="iq-card">
        <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
            <div class="iq-header-title">
                <h4 class="card-title text-primary border-left-heading">MONTHLY PO STATS&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo e(date('d-M-Y', strtotime($from))); ?> to <?php echo e(date('d-M-Y', strtotime($to))); ?></h4>
            </div>
        </div>
        <div class="iq-card-body p-0">
            <div class="container-fluid p-0">
                <div class="row">
                    <div class="col-md-12">
                        <canvas class="bar-charts" id="monthly-po-amount_-chart" data-data="<?php echo e(implode(',', array_values($billingData['po']))); ?>" data-labels="<?php echo e(implode(',', array_keys($billingData['po']))); ?>" data-legend-position="top" data-title-text="Amount" width="200" height="50"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-12">
    <div class="iq-card">
        <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
            <div class="iq-header-title">
                <h4 class="card-title text-primary border-left-heading">MONTHLY GRN STATS&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo e(date('d-M-Y', strtotime($from))); ?> to <?php echo e(date('d-M-Y', strtotime($to))); ?></h4>
            </div>
        </div>
        <div class="iq-card-body p-0">
            <div class="container-fluid p-0">
                <div class="row">
                    <div class="col-md-12">
                        <canvas class="bar-charts" id="monthly-grn-amount_-chart" data-data="<?php echo e(implode(',', array_values($billingData['grn']))); ?>" data-labels="<?php echo e(implode(',', array_keys($billingData['grn']))); ?>" data-legend-position="top" data-title-text="Amount" width="200" height="50"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-12">
    <div class="iq-card">
        <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
            <div class="iq-header-title">
                <h4 class="card-title text-primary border-left-heading">MONTHLY BILL STATS&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo e(date('d-M-Y', strtotime($from))); ?> to <?php echo e(date('d-M-Y', strtotime($to))); ?></h4>
            </div>
        </div>
        <div class="iq-card-body p-0">
            <div class="container-fluid p-0">
                <div class="row">
                    <div class="col-md-12">
                        <canvas class="bar-charts" id="monthly-bill-amount_-chart" data-data="<?php echo e(implode(',', array_values($billingData['bill']))); ?>" data-labels="<?php echo e(implode(',', array_keys($billingData['bill']))); ?>" data-legend-position="top" data-title-text="Amount" width="200" height="50"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/sites/34b/a/a55a29c214/procurement.zbridea.com/resources/views/pms/backend/pages/dashboard-partials/audit.blade.php ENDPATH**/ ?>