<?php $__env->startSection('nav'); ?>
<?php
   $user = auth()->user();
   $segment1 = request()->segment(1);
   $segment2 = request()->segment(2);
   $segment3 = request()->segment(3);
   $segment4 = request()->segment(4);
?>
   <nav class="iq-sidebar-menu">
      <ul id="iq-sidebar-toggle" class="iq-menu">
         <li>
            <a style="background: #daf0f3;color: #000;">
               <i class="las la-city "></i>  <span> <?php echo permitted_units(); ?> </span>
               
            </a>
         </li>
         <li class="<?php if($segment1 == ''): ?> active <?php endif; ?>">
            <a href="<?php echo e(url('/')); ?>" class="iq-waves-effect"><i class="las la-home"></i><span>Dashboard</span></a>
         </li>
         <li class="<?php if($segment1 == ''): ?> active <?php endif; ?>">
            <a target="__blank" href="<?php echo e(url('/pms')); ?>" class="iq-waves-effect"><i class="las la-list"></i><span>PMS</span></a>
         </li>
         <li class="<?php if($segment1 == ''): ?> active <?php endif; ?>">
            <a target="__blank" href="<?php echo e(route('my_project.my-project.index')); ?>" class="iq-waves-effect"><i class="las la-list"></i><span><?php echo e(__('My Project')); ?></span></a>
         </li>
         <li class="<?php if($segment1 == ''): ?> active <?php endif; ?>">
            <a target="__blank" href="<?php echo e(url('accounting')); ?>" class="iq-waves-effect"><i class="las la-list"></i><span><?php echo e(__('Accounting')); ?></span></a>
         </li>
         <?php if(auth()->user()->module_permission('HR')): ?>
         <li>
            <a href="<?php echo e(url('/hr')); ?>" class="iq-waves-effect"><i class="las la-users"></i><span>HR</span></a>
         </li> 
         <?php endif; ?>  
         
         <?php if(auth()->user()->hasRole('Super Admin')): ?>
         
         <?php endif; ?>                   
         
         
         <li class="<?php if($segment1 == 'ess'): ?> active <?php endif; ?>">
            <a href="#recruitment" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-user-tie"></i><span>ESS</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="recruitment" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li class="<?php if( $segment2=='leave_application'): ?> active <?php endif; ?>"><a href="<?php echo e(url('ess/leave_application')); ?>"><i class="las la-file-alt"></i>Leave Application</a></li>
               <li class="<?php if( $segment2=='out_side_request'): ?> active <?php endif; ?>"><a href="<?php echo e(url('ess/out_side_request/entry')); ?>"><i class="las la-file-alt"></i>Outside Request</a></li>
               <li class="<?php if( $segment2=='loan_application'): ?> active <?php endif; ?>"><a href="<?php echo e(url('ess/loan_application')); ?>"><i class="las la-file-alt"></i>Loan Application</a></li>
               
            </ul>
         </li>

         
         
      </ul>
   </nav>
<?php $__env->stopSection(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/user/menu.blade.php ENDPATH**/ ?>