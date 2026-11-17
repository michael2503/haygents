<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }} - Login & Multi-Role</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
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
                            <h4 class="font-weight-bold mb-0 text-white">Set New Password</h4>
                            <span class="text-muted small">Enter new password and confirm password</span>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: rgba(16, 185, 129, 0.15); border: 1px solid #10B981; color: #10B981;">
                            <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
                            <button type="button" class="close text-light" data-dismiss="alert" aria-label="Close">&times;</button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background-color: rgba(239, 68, 68, 0.15); border: 1px solid #EF4444; color: #EF4444;">
                            <i class="fa-solid fa-triangle-exclamation mr-2"></i> {{ session('error') }}
                            <button type="button" class="close text-light" data-dismiss="alert" aria-label="Close">&times;</button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background-color: rgba(239, 68, 68, 0.15); border: 1px solid #EF4444; color: #EF4444;">
                            <strong><i class="fa-solid fa-circle-exclamation mr-2"></i>Please check the input errors:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close text-light" data-dismiss="alert" aria-label="Close">&times;</button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.store') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}" name="" id="">
                        <div class="form-group mb-3">
                            <label class="small text-muted font-weight-bold">NEW PASSWORD</label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="small text-muted font-weight-bold">CONFIRM NEW PASSWORD</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block py-2 font-weight-bold" style="background-color: var(--accent-blue); border-radius: 8px;">
                            <i class="fa-solid fa-right-to-bracket mr-2"></i> RESET
                        </button>
                    </form>

                    <div class="mt-4 pt-3 border-top text-center" style="border-color: var(--border-color) !important;">
                        <a class="text-muted small" href="{{ route('login') }}">BACK TO LOGIN?</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
