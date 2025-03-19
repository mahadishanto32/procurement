<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title'); ?> - ERP</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo e(asset('images/mbm.ico')); ?> " />
    

    <!-- Styles -->
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo e(asset('assets/css/all.css')); ?>" media="all">
    <?php echo $__env->yieldPushContent('css'); ?>
    
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom.css')); ?>">
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/responsive.css')); ?>">
    <script src="<?php echo e(asset('js/app.js')); ?>"></script>
    <script>
        var count = 0;
        var refreshIntervalId =setInterval(function(){ 
            count++;
            jQuery(document).ready(function() {
                clearInterval(refreshIntervalId);
                jQuery("#load").fadeOut();
                jQuery("#loading").fadeOut("");
                
            });
            if( count == 5){
                clearInterval(refreshIntervalId);
                jQuery("#load").fadeOut();
                jQuery("#loading").fadeOut("");
            }
        }, 300);
    </script>
</head>
<body>
    <!-- loader Start -->
    <div id="loading">
        <div id="loading-center">
        </div>
    </div>
    <div id="main"></div>
    <!-- loader END -->
    <div id="app">
        <!-- Wrapper Start -->
        <div class="wrapper">
            <!-- Sidebar  -->
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
                    <?php echo $__env->yieldContent('nav'); ?>
                    <div class="p-3"></div>
                </div>
            </div>
            <!-- Page Content  -->
            <div id="content-page" class="content-page">
                <!-- TOP Nav Bar -->
                <div class="iq-top-navbar">
                   <div class="iq-navbar-custom">
                      <div class="iq-sidebar-logo">
                         <div class="top-logo">
                            <a href="index-2.html" class="logo">
                            <img src="<?php echo e(asset('assets/images/logo.png')); ?>" class="img-fluid" alt="">
                            <span>MBM Group</span>
                            </a>
                         </div>
                      </div>
                      <nav class="navbar navbar-expand-lg navbar-light p-0">
                         <div class="iq-search-bar">
                            <form action="#" class="searchbox">
                               <input type="text" class="text search-input" placeholder="Type here to search...">
                               <a class="search-link" href="#"><i class="las la-search"></i></a>
                            </form>
                         </div>
                         <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                         <i class="las la-ellipsis-h"></i>
                         </button>
                         <div class="iq-menu-bt align-self-center">
                            <div class="wrapper-menu">
                               <div class="main-circle"><i class="las la-ellipsis-h"></i></div>
                               <div class="hover-circle"><i class="las la-ellipsis-v"></i></div>
                            </div>
                         </div>
                         <div class="nav-item iq-full-screen">
                            <a href="#" class="iq-waves-effect" id="btnFullscreen"><i class="ri-fullscreen-line"></i></a>
                         </div>
                         <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ml-auto navbar-list">

                               <!-- <li class="nav-item">
                                  <a class="search-toggle iq-waves-effect language-title" href="#"><img src="<?php echo e(asset('assets/images/small/flag-01.png')); ?>" alt="img-flaf" class="img-fluid mr-1" style="height: 16px; width: 16px;" /> English <i class="ri-arrow-down-s-line"></i></a>
                                  <div class="iq-sub-dropdown">
                                     <a class="iq-sub-card" href="#"><img src="<?php echo e(asset('assets/images/small/flag-02.png')); ?>" alt="img-flaf" class="img-fluid mr-2" />French</a>
                                     <a class="iq-sub-card" href="#"><img src="<?php echo e(asset('assets/images/small/flag-03.png')); ?>" alt="img-flaf" class="img-fluid mr-2" />Spanish</a>
                                     <a class="iq-sub-card" href="#"><img src="<?php echo e(asset('assets/images/small/flag-04.png')); ?>" alt="img-flaf" class="img-fluid mr-2" />Italian</a>
                                     <a class="iq-sub-card" href="#"><img src="<?php echo e(asset('assets/images/small/flag-05.png')); ?>" alt="img-flaf" class="img-fluid mr-2" />German</a>
                                     <a class="iq-sub-card" href="#"><img src="<?php echo e(asset('assets/images/small/flag-06.png')); ?>" alt="img-flaf" class="img-fluid mr-2" />Japanese</a>
                                  </div>
                               </li> -->

                               
                               
                            </ul>
                         </div>
                         <ul class="navbar-list">
                            <li>
                               <a href="#" class="search-toggle iq-waves-effect d-flex align-items-center">
                                  <?php if(auth()->user()->employee): ?>
                                  <img src='<?php echo e(emp_profile_picture(auth()->user()->employee)); ?>' class="img-fluid rounded mr-3" alt="<?php echo e(auth()->user()->name); ?>" >
                                  <?php else: ?>
                                    <img class="img-fluid rounded mr-3" src="<?php echo e(asset('assets/images/user/09.jpg')); ?> ">
                                  <?php endif; ?>
                                  <div class="caption">
                                     <h6 class="mb-0 line-height"><?php echo e(auth()->user()->name); ?></h6>
                                     <span class="font-size-12">Available</span>
                                  </div>
                               </a>
                               <div class="iq-sub-dropdown iq-user-dropdown">
                                  <div class="iq-card shadow-none m-0">
                                     <div class="iq-card-body p-0 ">
                                        <div class="bg-primary p-3">
                                           <h5 class="mb-0 text-white line-height">Hello <?php echo e(auth()->user()->name); ?></h5>
                                           <span class="text-white font-size-12">Available</span>
                                        </div>
                                        <a href="<?php echo e(url('profile')); ?>" class="iq-sub-card iq-bg-primary-hover">
                                           <div class="media align-items-center">
                                              <div class="rounded iq-card-icon iq-bg-primary">
                                                 <i class="f-18 las la-user-tie"></i>
                                              </div>
                                              <div class="media-body ml-3">
                                                 <h6 class="mb-0 ">My Profile</h6>
                                                 <p class="mb-0 font-size-12">View personal profile details.</p>
                                              </div>
                                           </div>
                                        </a>
                                        
                                        <a href="<?php echo e(url('user/change-password')); ?>" class="iq-sub-card iq-bg-primary-hover">
                                           <div class="media align-items-center">
                                              <div class="rounded iq-card-icon iq-bg-primary">
                                                 <i class="f-18 las la-key"></i>
                                              </div>
                                              <div class="media-body ml-3">
                                                 <h6 class="mb-0 ">Account settings</h6>
                                                 <p class="mb-0 font-size-12">Manage your password.</p>
                                              </div>
                                           </div>
                                        </a>
                                        <div class="d-inline-block w-100 text-center p-3">
                                           
                                           <a class="bg-primary iq-sign-btn" role="button" href="<?php echo e(route('logout')); ?>"
                                              onclick="event.preventDefault();
                                              document.getElementById('logout-form').submit();">
                                              <?php echo e(__('Sign out')); ?> <i class="ri-login-box-line ml-2"></i>
                                           </a>

                                           <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                                               <?php echo csrf_field(); ?>
                                           </form>
                                        </div>
                                     </div>
                                  </div>
                               </div>
                            </li>
                         </ul>
                      </nav>
                   </div>
                </div>
                
                <main class="">
                    <?php echo $__env->yieldContent('content'); ?>
                </main>
                <!-- Footer -->
                <footer class="bg-white iq-footer mr-0">
                   <div class="container-fluid">
                      <div class="row">
                         <div class="col-lg-6">
                            <ul class="list-inline mb-0">
                               <li class="list-inline-item"><a href="#">Privacy Policy</a></li>
                               <li class="list-inline-item"><a href="#">Terms of Use</a></li>
                            </ul>
                         </div>
                         <div class="col-lg-6 text-right">
                            Design & developed for <a>Technocrats group</a> By <a href="https://bizzsol.com.bd/">Procurement Solution</a>.
                         </div>
                      </div>
                   </div>
                </footer>
                <!-- Footer END -->
            </div>
            <div class="app-loader">
                <i class="fa fa-spinner fa-spin"></i>
            </div>
            

        </div>
    </div>
    <!-- Scripts -->
    
    <script src="<?php echo e(asset('assets/js/all.js')); ?>"></script>
    <!-- Custom JavaScript -->
    <?php echo $__env->yieldPushContent('js'); ?>
    
    <script src="<?php echo e(asset('assets/js/custom.js')); ?>"></script>
    <script src="<?php echo e(asset('js/custom.js')); ?>"></script>
    
</body>
</html>
<?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/layouts/app.blade.php ENDPATH**/ ?>