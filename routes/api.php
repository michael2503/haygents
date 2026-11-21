<?php

// use App\Http\Controllers\ApiController\AuthController;
// use App\Http\Controllers\ApiController\PropertyApiController;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;

// /*
// |--------------------------------------------------------------------------
// | API Routes
// |--------------------------------------------------------------------------
// |
// | Here is where you can register API routes for your application. These
// | routes are loaded by the RouteServiceProvider and all of them will
// | be assigned to the "api" middleware group. Make something great!
// |
// */

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::group(['prefix' => 'v1'], function() {

//     Route::controller(AuthController::class)->group(function () {
//         Route::group(['prefix' => 'auth'], function() {
//             Route::post('/login', 'login');
//             Route::post('/two-fa/verify', 'verifyTwoFa');

//             Route::group(['prefix' => 'forgot-password'], function() {
//                 Route::post('/', 'forgotPassword');
//                 Route::post('/verify/email', 'verifyForgotPasword');
//                 Route::post('/reset', 'forgotPasswordReset');
//             });
//         });
//     });


//     Route::group(['prefix' => 'haygents'], function() {
//         Route::middleware('auth:api')->group(function() {

//             Route::controller(PropertyApiController::class)->group(function () {
//                 Route::group(['prefix' => 'properties'], function() {
//                     Route::get('/', 'index');
//                     Route::get('/{property:slug}', 'show');
//                 });
//             });

//         });
//     });

// });


use App\Http\Controllers\Api\V1\AdminOnboardingController;
use App\Http\Controllers\Api\V1\MobileAuthController;
use App\Http\Controllers\Api\V1\OnboardingController;
use App\Http\Middleware\ApiTokenAuthenticate;
use App\Http\Middleware\EnsureApprovedAccount;
use App\Http\Middleware\EnsureSuperAdmin;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public mobile registration & login endpoints
    Route::middleware('throttle:15,1')->group(function () {
        Route::post('/auth/register', [MobileAuthController::class, 'register']);
        Route::post('/auth/login', [MobileAuthController::class, 'login']);
    });

    // Dynamic options & database-backed service catalogs for mobile registration forms
    Route::get('/onboarding/meta', [OnboardingController::class, 'metadata']);
    Route::get('/onboarding/professional/services', [OnboardingController::class, 'servicesByProfession']);

    // Authenticated mobile user onboarding & verification flow
    Route::middleware(ApiTokenAuthenticate::class)->group(function () {
        Route::get('/auth/me', [MobileAuthController::class, 'me']);
        Route::post('/auth/logout', [MobileAuthController::class, 'logout']);

        // Step 2: Email & Phone verification
        Route::middleware('throttle:10,1')->group(function () {
            Route::post('/auth/verify-email', [MobileAuthController::class, 'verifyEmail']);
            Route::post('/auth/verify-phone', [MobileAuthController::class, 'verifyPhone']);
            Route::post('/auth/resend-verification', [MobileAuthController::class, 'resendVerificationCode']);
        });

        // Step 3 — Sub-step 1: Identity Check (Agent, Company, Professional)
        Route::prefix('onboarding/identity')->group(function () {
            Route::post('/legal-details', [OnboardingController::class, 'legalDetails']);
            Route::post('/government-id', [OnboardingController::class, 'governmentId']);
            Route::post('/proof-of-address', [OnboardingController::class, 'proofOfAddress']);
            Route::post('/passport-photo', [OnboardingController::class, 'passportPhoto']);
        });

        // Step 3 — Sub-step 2: Professional Profile (Lawyer, Surveyor, Valuer)
        Route::prefix('onboarding/professional')->group(function () {
            Route::post('/profession', [OnboardingController::class, 'selectProfession']);
            Route::post('/practice-details', [OnboardingController::class, 'practiceDetails']);
            Route::post('/offerings', [OnboardingController::class, 'offerings']);
            Route::post('/public-profile', [OnboardingController::class, 'publicProfile']);
            Route::post('/credentials/document', [OnboardingController::class, 'uploadCredentialDocument']);
            Route::post('/credentials', [OnboardingController::class, 'uploadCredentials']);
        });

        // Step 3 — Sub-step 2: Partner Profile (Agent, Company)
        Route::post('/onboarding/partner/profile', [OnboardingController::class, 'partnerProfile']);

        // Gated Dashboard Entry (requires email/phone verification, completed onboarding, and admin approval for partners)
        Route::get('/dashboard/status', [OnboardingController::class, 'dashboardStatus'])
            ->middleware(EnsureApprovedAccount::class);

        // Super-Admin only: Staff onboarding & partner/professional approval
        Route::prefix('admin')
            ->middleware(EnsureSuperAdmin::class)
            ->group(function () {
                Route::post('/staff', [AdminOnboardingController::class, 'storeStaff']);
                Route::get('/approvals/pending', [AdminOnboardingController::class, 'pendingApprovals']);
                Route::get('/approvals/{user}', [AdminOnboardingController::class, 'showApplicant']);
                Route::post('/approvals/{user}/approve', [AdminOnboardingController::class, 'approveUser']);
                Route::post('/approvals/{user}/reject', [AdminOnboardingController::class, 'rejectUser']);
            });
    });
});
