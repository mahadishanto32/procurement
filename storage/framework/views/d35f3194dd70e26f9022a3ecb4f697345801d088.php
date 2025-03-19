<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo e($title); ?></title>
    <link rel="shortcut icon" href="<?php echo e(asset('images/mbm.ico')); ?> " />
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet" media='screen,print'>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/all.css')); ?>" media='screen,print'>
    <?php echo $__env->yieldPushContent('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom.css?v=1.3')); ?>" media='screen,print'>
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/responsive.css')); ?>" media='screen,print'>
    <style type="text/css">
        @media  print {
            .print_the_pages {
                display: none;
            }

            *{
                background-color: white !important;
            }
        }
        
        .list-unstyled .ratings {
            display: none;
        }

        .print_invoice{
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div id="app">

        <div id="content-page" class="container">
            <main class="" style="padding-bottom: 0;">
                <div id="main-body" class="">
                    <div class="main-content">
                        <div class="main-content-inner print_invoice">
                            <?php if(isset($note->id)): ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel p-1">
                                        <div class="panel-body bg-white" style="border: 1px dashed #ccc !important">
                                            <h3 class="text-center mb-3"><strong>TECHNOCRATS ERP</strong></h3>
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td style="border-top: none !important;width: 20%">Reference</td>
                                                        <td style="border-top: none !important;width: 80%">
                                                            <?php echo e($note->grn_reference_no); ?>

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border-top: none !important;width: 20%">Date</td>
                                                        <td style="border-top: none !important;width: 80%">
                                                            <?php echo e(date('d-m-Y',strtotime($note->received_date))); ?>

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border-top: none !important;width: 20%">Challan</td>
                                                        <td style="border-top: none !important;width: 80%">
                                                            <?php echo e($note->challan); ?>

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border-top: none !important;width: 20%">Supplier</td>
                                                        <td style="border-top: none !important;width: 80%">
                                                            <?php echo e($note->relPurchaseOrder->relQuotation->relSuppliers->name); ?>

                                                            &nbsp;&nbsp;|&nbsp;&nbsp;
                                                            <?php echo e($note->relPurchaseOrder->relQuotation->relSuppliers->email); ?>

                                                            &nbsp;&nbsp;|&nbsp;&nbsp;
                                                            <?php echo e($note->relPurchaseOrder->relQuotation->relSuppliers->phone); ?>

                                                            &nbsp;&nbsp;|&nbsp;&nbsp;
                                                            <?php echo e($note->relPurchaseOrder->relQuotation->relSuppliers->address); ?>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="table mb-4">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 55%">Product</th>
                                                        <th style="width: 20%;text-align: center">Warhouse</th>
                                                        <th style="width: 10%;text-align: center">Unit</th>
                                                        <th style="width: 15%" class="text-right">Qty</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                        $total_received_qty = 0; 
                                                    ?>
                                                    <?php if($items->count() > 0): ?>
                                                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php 
                                                        $total_received_qty += $item->relGoodsReceivedItemStockIn->sum('received_qty'); 
                                                    ?>
                                                    <tr>
                                                        <td><?php echo e($item->relProduct->name); ?> (<?php echo e(getProductAttributes($item->product_id)); ?>)</td>
                                                        <td style="text-align:center"><?php echo e(isset($item->relGoodsReceivedItemStockIn[0]->relWarehouse->name) ? $item->relGoodsReceivedItemStockIn[0]->relWarehouse->name : ''); ?></td>
                                                        <td style="text-align:center"><?php echo e($item->relProduct->productUnit->unit_name); ?></td>
                                                        <td class="text-right"><?php echo e(number_format($item->relGoodsReceivedItemStockIn->sum('received_qty'),0)); ?></td>
                                                    </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php endif; ?>

                                                    <tr>
                                                        <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                                        <td class="text-right"><strong><?php echo e(number_format($total_received_qty, 0)); ?></strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <h6 class="text-center mb-3">Thank you for doing business with us!</h6>
                                            <center>
                                                <img src="data:image/png;base64,<?php echo DNS1D::getBarcodePNG($note->grn_reference_no, 'C39',1,33); ?>" alt="barcode"/>
                                            </center>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="col-md-12 mb-3">
                                <center>
                                    <a href="#" class="btn btn-info btn-sm print_the_pages text-center">
                                        <i class="las la-print" aria-hidden="true"></i>
                                        <span>Print <?php echo e($title); ?></span></a>
                                    </center>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

</body>
<script src="<?php echo e(asset('js/app.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/all.js')); ?>"></script>
<script>
    const PrintPage=()=>{
        $('.print_the_pages').on('click', function () {
            var restorepage = $('body').html();
            var printcontent = $('.print_invoice').clone();
            $('body').empty().html(printcontent);
            window.print();
            $('body').html(restorepage)

            return false;
        }); 
    };
    PrintPage();

    $('.print_the_pages').trigger('click');
</script>
</html><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/grn-stock-in/grn-slip.blade.php ENDPATH**/ ?>