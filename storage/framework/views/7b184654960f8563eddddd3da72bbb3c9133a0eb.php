<?php if(isset($products[0])): ?>
    <?php
        $total = 0;
    ?>
    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $qty = $inventoryDetails->where('product_id', $product->id)->sum('qty');
            $total += $qty;
        ?>
        <tr>
            <td><?php echo e($key+1); ?></td>
            <td><?php echo e($product->category->name); ?></td>
            <td><a onclick="showWarehouseStocks('<?php echo e($product->id); ?>')" class="text-primary"><?php echo e($product->name); ?></a></td>
            <td><?php echo selectedProductAttributes($product->id, $attributeOptions); ?></td>
            <td class="text-right"><?php echo e($qty); ?></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <tr>
        <td colspan="4"><strong>Total:</strong></td>
        <td class="text-right"><strong><?php echo e($total); ?></strong></td>
    </tr>
<?php else: ?>
    <tr>
        <td colspan="5" class="text-center"><h4>No Product Found for these attributes!</h4></td>
    </tr>
<?php endif; ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/inventory/inventory-stock/stocks.blade.php ENDPATH**/ ?>