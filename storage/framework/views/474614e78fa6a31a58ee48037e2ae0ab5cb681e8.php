<!doctype html>
<html lang="en">

<head>
      <!-- Required meta tags -->
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>PROCUREMENT - ERP</title>
      <!-- Fav icon -->
      <link rel="shortcut icon" href="<?php echo e(asset('images/mbm.ico')); ?> " />

      <!-- Styles -->
      <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">

      <!-- Style CSS -->
      <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">  
      <!-- Responsive CSS -->
      <link rel="stylesheet" href="<?php echo e(asset('assets/css/responsive.css')); ?>">
      <style>
          .sign-in-page{
            position: absolute;
            top: 35%;
            transform: translate(0, -35%);
            padding: 10px;
            width: 100%;
            height: auto;
          }
          .sign-in-detail {
            max-width: 100%;
            height: 100%;
          }
      </style>
</head>
   <body>
      <!-- loader END -->
      <div id="app">
        <!-- Sign in Start -->
        <section class="sign-in-page">
            <div class="container sign-in-page-bg mt-4 p-0">
                <div class="row no-gutters pl-5">
                    <div class="col-md-6 text-center ">
                        <div class="sign-in-detail text-white">
                            <div >
                                <div class="item login-slider">
                                    <img src="<?php echo e(asset('images/login/2.jpg')); ?>" class="img-fluid mb-4 radius-10" alt="logo">
                                    <h4 class="mb-1 text-white">Procurement-ERP</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 position-relative">
                        <div class="sign-in-from">
                            
                            <form method="POST" action="<?php echo e(route('login')); ?>" class="mt-4">
                              <?php echo csrf_field(); ?>
                                <a class="sign-in-logo text-center mb-3 " href="#">
                                    <img src="<?php echo e(asset('images/login/logo.png')); ?>" class="img-fluid" alt="MBM">
                                </a>
                                <div class="form-group">
                                    <label for="email">Email address</label>
                                    <input name="email" type="email" class="form-control mb-0 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email" value="<?php echo e(old('email')); ?>" placeholder="Enter email" autofocus="autofocus">
                                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                      <span class="invalid-feedback" role="alert">
                                          <strong><?php echo e($message); ?></strong>
                                      </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    
                                    <input name="password" type="password" class="form-control mb-0 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="password" placeholder="Password" value="<?php echo e(old('password')); ?>">
                                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                      <span class="invalid-feedback" role="alert">
                                          <strong><?php echo e($message); ?></strong>
                                      </span>
                                  <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="d-inline-block w-100">
                                    <div class="custom-control custom-checkbox d-inline-block mt-2 pt-1">
                                        <input type="checkbox" class="custom-control-input" id="customCheck1">
                                        <label class="custom-control-label" for="customCheck1">Remember Me</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary float-right">Sign in</button>
                                </div>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Sign in END -->
      </div>
      <script src="<?php echo e(asset('js/app.js')); ?>"></script>
      <script>
        $(document).ready(function() {
          $("#email").focus();  
        });
      </script>
   </body>

</html><?php /**PATH /home/sites/34b/a/a55a29c214/procurement.zbridea.com/resources/views/auth/login.blade.php ENDPATH**/ ?>