
<div class="card card-timeline px-2 border-none">
	<?php if(count($requisition->requisitionTracking)>0): ?>
	<?php 
		$numItems = count($requisition->requisitionTracking);
		$note=''; 
		$status=''; 
		$i = 0;
		
		$tracking=['la la-folder-open'=>'Draft','la la-clock-o'=>'Pending','las la-check'=>'Approved','las la-spinner'=>'Processing','las la-truck'=>'Delivered','las la-receipt'=>'Received'];
		$tracking_array=[];
	?>
	<ul class="bs4-order-tracking">
		<?php $__currentLoopData = $requisition->requisitionTracking()->groupBy('status')->orderBy('id','asc')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<?php 
			if($values=='halt'){
				$note=$values->note;
			}
			
			array_push($tracking_array, ucfirst($values->status))
		?>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

		<?php $__currentLoopData = $tracking; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $tr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<li class="step <?php echo e((in_array($tr,$tracking_array))?'active':''); ?>">
			<div><i class="<?php echo e($key); ?>"></i></div> <?php echo e($tr); ?>

		</li>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	</ul>
	
	<?php if(!empty($note)): ?>
		<h5 class="text-center"><b>Notes: </b><?php echo e($note); ?></h5>
	<?php endif; ?>
	
	<?php endif; ?>
</div><?php /**PATH /home/sites/34b/a/a55a29c214/procurement.zbridea.com/resources/views/pms/backend/pages/requisitions/tracking.blade.php ENDPATH**/ ?>