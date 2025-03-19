<ul class="pl-3">
    <li>Category: <strong><?php echo e($product->category->name); ?></strong></li>
    <li>Product Name: <strong><?php echo e($product->name); ?></strong></li>
    <li>Attributes: <?php echo selectedProductAttributes($product->id, $attributeOptions); ?></li>
</ul>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th style="width: 70%">Warehouse</th>
            <th style="width: 30%" class="text-right">Stock</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $inventoryDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td style="width: 70%"><?php echo e($values->relWarehouse->name); ?></td>
            <td style="width: 30%" class="text-right"><?php echo e($values->qty); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <tr>
            <td style="width: 70%"><strong>Total:</strong></td>
            <td style="width: 30%" class="text-right">
                <strong><?php echo e($inventoryDetails->sum('qty')); ?></strong>
            </td>
        </tr>
    </tbody>
</table><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/inventory/inventory-stock/warehouse.blade.php ENDPATH**/ ?>