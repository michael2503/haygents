<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }} - School Onboarding & Registration</title>
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
        }
        body {
            background-color: var(--bg-primary);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            padding: 40px 15px;
        }
        .card-custom {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
        }
        .form-control, .custom-select {
            background-color: #0F172A;
            border: 1px solid var(--border-color);
            color: #FFFFFF;
            border-radius: 8px;
            height: 46px;
        }
        .form-control:focus, .custom-select:focus {
            background-color: #0F172A;
            border-color: #3B82F6;
            color: #FFFFFF;
        }
        .plan-select-card {
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.2s;
            background-color: #0F172A;
        }
        .plan-select-card:hover, .plan-select-card.active {
            border-color: #3B82F6;
            background-color: rgba(59, 130, 246, 0.08);
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 900px;">
        <div class="text-center mb-4">
            <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm mb-3">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back to Login
            </a>
            <h2 class="font-weight-bold text-white">Register Your Institution</h2>
            <p class="text-muted">Launch your dedicated, multi-branch school tenant in seconds</p>
        </div>

        <div class="card-custom p-4 p-md-5">
            <form action="{{ route('school.register.process') }}" method="POST">
                @csrf

                <h5 class="text-primary font-weight-bold mb-3"><i class="fa-solid fa-school mr-2"></i> 1. School Information</h5>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="small text-muted font-weight-bold">SCHOOL NAME *</label>
                        <input type="text" name="school_name" class="form-control" placeholder="e.g. St. Jude High School" required value="{{ old('school_name') }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="small text-muted font-weight-bold">SCHOOL URL / DOMAIN NAME *</label>
                        <div class="input-group">
                            <input type="text" name="school_slug" id="school_slug" class="form-control" placeholder="knoxfield" required value="{{ old('school_slug') }}" oninput="refreshPreview(this.value)" style="border-right: none;">
                            <div class="input-group-append">
                                <span class="input-group-text text-muted" style="background:#0F172A; border-color:var(--border-color); font-size:13px;">.{{ strtolower(env('APP_NAME')) }}.com</span>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-1">
                            Your portal URL: <strong class="text-info" id="slug-preview">https://knoxfield.{{ strtolower(env('APP_NAME')) }}.com</strong>
                        </small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="small text-muted font-weight-bold">REGISTRATION / GOVT NO.</label>
                        <input type="text" name="registration_number" class="form-control" placeholder="e.g. MOE/EDU/2026/894" value="{{ old('registration_number') }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="small text-muted font-weight-bold">SCHOOL OFFICIAL EMAIL *</label>
                        <input type="email" name="email" class="form-control" placeholder="admin@stjude.edu" required value="{{ old('email') }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="small text-muted font-weight-bold">OFFICIAL PHONE *</label>
                        <input type="text" name="phone" class="form-control" placeholder="+1 (555) 234-5678" required value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="small text-muted font-weight-bold">Total Student</label>
                        <input type="number" name="total_student" class="form-control" placeholder="Min. of 20 students" value="{{ old('total_student') }}">
                    </div>
                    <div class="col-md-8 form-group">
                        <label class="small text-muted font-weight-bold">MOTTO</label>
                        <input type="text" name="motto" class="form-control" placeholder="All Round Education" value="{{ old('motto') }}">
                    </div>
                    <div class="col-12 form-group">
                        <label class="small text-muted font-weight-bold">CAMPUS ADDRESS</label>
                        <input type="text" name="address" class="form-control" placeholder="104 Academic Blvd, Metro City" value="{{ old('address') }}">
                    </div>
                </div>

                <hr style="border-color: var(--border-color);" class="my-4">

                <h5 class="text-primary font-weight-bold mb-3"><i class="fa-solid fa-user-shield mr-2"></i> 2. Primary Administrator</h5>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="small text-muted font-weight-bold">ADMIN/PRINCIPAL FULL NAME *</label>
                        <input type="text" name="admin_name" class="form-control" placeholder="Principal Dr. Sarah Jenkins" required value="{{ old('admin_name') }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="small text-muted font-weight-bold">ADMIN LOGIN USERNAME * <br><small>(Only letters, numbers, underscores & hyphens are allowed)</small></label>
                        <input type="text" name="username" class="form-control" placeholder="" required value="{{ old('username') }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="small text-muted font-weight-bold">ADMIN PASSWORD *</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="small text-muted font-weight-bold">INITIAL MAIN BRANCH NAME</label>
                        <input type="text" name="branch_name" class="form-control" placeholder="Main Campus" value="Main Campus">
                    </div>
                </div>

                <hr style="border-color: var(--border-color);" class="my-4">

                {{-- <h5 class="text-primary font-weight-bold mb-3"><i class="fa-solid fa-cubes mr-2"></i> 3. Select Initial Plan</h5>
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="plan-select-card active" onclick="selectPlan(1, this)">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="plan1" name="package_id" value="1" class="custom-control-input" checked>
                                <label class="custom-control-label font-weight-bold text-white" for="plan1">Starter Plan</label>
                            </div>
                            <div class="text-primary font-weight-bold mt-2">$49 <span class="small text-muted">/ term</span></div>
                            <ul class="list-unstyled small text-muted mt-2 mb-0" style="font-size: 11px;">
                                <li>• Up to 200 Students</li>
                                <li>• 1 Branch</li>
                                <li>• Attendance & Grades</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="plan-select-card" onclick="selectPlan(2, this)">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="plan2" name="package_id" value="2" class="custom-control-input">
                                <label class="custom-control-label font-weight-bold text-white" for="plan2">Professional</label>
                            </div>
                            <div class="text-primary font-weight-bold mt-2">$129 <span class="small text-muted">/ term</span></div>
                            <ul class="list-unstyled small text-muted mt-2 mb-0" style="font-size: 11px;">
                                <li>• Up to 800 Students</li>
                                <li>• Multi-Branch Allowed</li>
                                <li>• Library, Finance & Bus</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="plan-select-card" onclick="selectPlan(3, this)">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="plan3" name="package_id" value="3" class="custom-control-input">
                                <label class="custom-control-label font-weight-bold text-white" for="plan3">Enterprise</label>
                            </div>
                            <div class="text-primary font-weight-bold mt-2">$299 <span class="small text-muted">/ term</span></div>
                            <ul class="list-unstyled small text-muted mt-2 mb-0" style="font-size: 11px;">
                                <li>• Up to 3,000 Students</li>
                                <li>• Unlimited Branches</li>
                                <li>• Full CBT Engine & AI</li>
                            </ul>
                        </div>
                    </div>
                </div> --}}

                <button type="submit" class="btn btn-primary btn-block py-3 font-weight-bold" style="border-radius: 8px;">
                    <i class="fa-solid fa-check-circle mr-2"></i> Complete Onboarding & Open Portal
                </button>
            </form>
        </div>
    </div>

    <script>
        function selectPlan(id, element) {
            document.querySelectorAll('.plan-select-card').forEach(c => c.classList.remove('active'));
            element.classList.add('active');
            document.getElementById('plan' + id).checked = true;
        }

        function updateSlug(val) {
            const slugInput = document.getElementById('school_slug');
            if (!slugInput.dataset.manual) {
                const clean = val.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugInput.value = clean;
                refreshPreview(clean);
            }
        }

        function refreshPreview(val) {
            let appName = "{{ env('APP_NAME') }}";
            const preview = document.getElementById('slug-preview');
            const clean = val.toLowerCase().replace(/[^a-z0-9\-]/g, '');
            preview.innerText = 'https://' + (clean || 'knoxfield') + '.' + appName.toLowerCase() + '.com';
            document.getElementById('school_slug').dataset.manual = "true";
        }
    </script>
</body>
</html>
