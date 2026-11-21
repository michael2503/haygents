<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(env('APP_NAME')); ?> - Login & Multi-Role</title>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/all.min.css')); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0F172A;
            --bg-card: #1E293B;
            --border-color: #334155;
            --text-main: #FFFFFF;
            --text-muted: #94A3B8;
            --accent-blue: #2563EB;
        }
        body {
            background-color: var(--bg-primary);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            /* min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px; */
        }
        .login-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            overflow: hidden;
            /* width: 100%;
            max-width: 1100px; */
            padding: 30px 20px;
            margin-top: 50px
        }
        .role-btn {
            background-color: #0F172A;
            border: 1px solid var(--border-color);
            color: var(--text-main);
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            text-decoration: none !important;
        }
        .role-btn:hover {
            border-color: #3B82F6;
            background-color: #1E3A8A;
            color: #FFFFFF;
            transform: translateY(-2px);
        }
        .form-control {
            background-color: #0F172A;
            border: 1px solid var(--border-color);
            color: #FFFFFF;
            border-radius: 8px;
            height: 46px;
        }
        .form-control:focus {
            background-color: #0F172A;
            border-color: #3B82F6;
            color: #FFFFFF;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        }
        .brand-badge {
            background: linear-gradient(135deg, #2563EB, #7C3AED);
            border-radius: 10px;
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div class="container">

        <div class="row d-flex justify-content-center">
            <div class="col-lg-5">

                <div class="login-card">
                    <div class="d-flex align-items-center mb-4">
                        <div class="brand-badge mr-3">
                            <i class="fa-solid fa-graduation-cap text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-weight-bold mb-0 text-white"><?php echo e(env('APP_NAME')); ?> Login</h4>
                            <span class="text-muted small">Enter credentials for your <?php echo e(env('APP_NAME')); ?> account</span>
                        </div>
                    </div>

                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: rgba(16, 185, 129, 0.15); border: 1px solid #10B981; color: #10B981;">
                            <i class="fa-solid fa-circle-check mr-2"></i> <?php echo e(session('success')); ?>

                            <button type="button" class="close text-light" data-dismiss="alert" aria-label="Close">&times;</button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background-color: rgba(239, 68, 68, 0.15); border: 1px solid #EF4444; color: #EF4444;">
                            <i class="fa-solid fa-triangle-exclamation mr-2"></i> <?php echo e(session('error')); ?>

                            <button type="button" class="close text-light" data-dismiss="alert" aria-label="Close">&times;</button>
                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background-color: rgba(239, 68, 68, 0.15); border: 1px solid #EF4444; color: #EF4444;">
                            <strong><i class="fa-solid fa-circle-exclamation mr-2"></i>Please check the input errors:</strong>
                            <ul class="mb-0 mt-1">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <button type="button" class="close text-light" data-dismiss="alert" aria-label="Close">&times;</button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('login')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="form-group mb-3">
                            <label class="small text-muted font-weight-bold">Username</label>
                            <div class="input-group">
                                <input type="text" name="email" class="form-control" value="<?php echo e(old('email')); ?>" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="small text-muted font-weight-bold">PASSWORD</label>
                            <input type="password" name="password" class="form-control" placeholder="" required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="remember" name="remember" checked>
                                <label class="custom-control-label small text-muted" for="remember">Remember me</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block py-2 font-weight-bold" style="background-color: var(--accent-blue); border-radius: 8px;">
                            <i class="fa-solid fa-right-to-bracket mr-2"></i> Sign In to School
                        </button>
                    </form>

                    <div class="mt-4 pt-3 border-top text-center" style="border-color: var(--border-color) !important;">
                        <a class="text-muted small" href="<?php echo e(route('password.request')); ?>">FORGOT PASSWORD?</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
<?php /**PATH C:\Laravel\haygents\resources\views/auth/login.blade.php ENDPATH**/ ?>