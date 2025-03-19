<div class="iq-sidebar">
    <input type="hidden" value="<?php echo e(url('/')); ?>" id="base_url">
    <div class="iq-sidebar-logo d-flex justify-content-between">
        <a href="<?php echo e(url('/')); ?>">
            <img src="<?php echo e(asset('images/mbm-logo-w.png')); ?>" class="img-fluid" alt="MBM">
            
        </a>
        <div class="iq-menu-bt-sidebar">
            <div class="iq-menu-bt align-self-center">
                <div class="wrapper-menu">
                    <div class="main-circle"><i class="las la-ellipsis-h"></i></div>
                    <div class="hover-circle"><i class="las la-ellipsis-v"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div id="sidebar-scrollbar">

        <?php
            $user = auth()->user();
            $segment1 = request()->segment(1);
            $segment2 = request()->segment(2);
            $segment3 = request()->segment(3);
            $segment4 = request()->segment(4);
            $segment5 = request()->segment(5);
        ?>

        <nav class="iq-sidebar-menu">
            <ul id="iq-sidebar-toggle" class="iq-menu">

                <?php $__empty_1 = true; $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(json_decode($menu->slug,true))): ?>
                    <?php
                    $activeSubmenuNumber=count($menu->activeSubMenu);
                    $firstLiActiveClass='';
                    if (Request()->path()==$menu->url){
                        $firstLiActiveClass='active';
                    }
                    ?>
                    <li class="iq-menu-main <?php echo e($firstLiActiveClass); ?>">
                        

                        <?php
                        $menuToggle='';
                        $collapsed='';
                        $dropDownIcon='';
                        $url=URL::to($menu->url);
                        if ($activeSubmenuNumber>0)
                        {
                            $menuToggle='collapse';
                            $collapsed='collapsed';
                            $dropDownIcon="las la-angle-right iq-arrow-right";
                            $url='#'.\Str::slug( $menu->name.$menu->id);
                        }
                        ?>
                        <a href="<?php echo e($url); ?>" class="iq-waves-effect <?php echo e($collapsed); ?>" data-toggle="<?php echo e($menuToggle); ?>" target="<?php echo e($menu->open_new_tab==\App\Models\PmsModels\Menu\Menu::OPEN_NEW_TAB?'_blank':''); ?>" aria-expanded="false"><i class="<?php echo e($menu->icon_class); ?>"></i><span><?php echo e(__($menu->name)); ?></span><i class="<?php echo e($dropDownIcon); ?>"></i></a>

                        <?php if($activeSubmenuNumber>0): ?>

                            <ul id="<?php echo e(\Str::slug( $menu->name.$menu->id)); ?>" class="iq-submenu <?php echo e($menuToggle); ?>" data-parent="#iq-sidebar-toggle">
                                
                                <?php $__currentLoopData = $menu->activeSubMenu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subMenu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check(json_decode($subMenu->slug,true))): ?>
                                        <li class="<?php echo e(Request()->path() == $subMenu->url?'active':''); ?>">
                                            <a  href="<?php echo e(URL::to($subMenu->url)); ?>"><i class="las la-arrow-right" target="<?php echo e($subMenu->open_new_tab==\App\Models\PmsModels\Menu\SubMenu::OPEN_NEW_TAB?'_blank':''); ?>"></i><?php echo e(__($subMenu->name)); ?></a>
                                        </li>
                                    <?php endif; ?>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li> No Menu Data Found</li>
                <?php endif; ?>

            </ul>
        </nav>

        <div class="p-3"></div>
    </div>
</div><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/menus/left-menu.blade.php ENDPATH**/ ?>