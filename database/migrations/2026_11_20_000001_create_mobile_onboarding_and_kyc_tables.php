<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'account_type')) {
                $table->string('account_type', 40)->default('customer')->after('email');
            }
            if (!Schema::hasColumn('users', 'staff_role')) {
                $table->string('staff_role', 40)->nullable()->after('account_type');
            }
            if (!Schema::hasColumn('users', 'dial_code')) {
                $table->string('dial_code', 10)->nullable()->after('staff_role');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->nullable()->unique()->after('dial_code');
            }
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country', 100)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state', 100)->nullable()->after('country');
            }
            if (!Schema::hasColumn('users', 'lga')) {
                $table->string('lga', 120)->nullable()->after('state');
            }
            if (!Schema::hasColumn('users', 'referral_code')) {
                $table->string('referral_code', 32)->nullable()->unique()->after('lga');
            }
            if (!Schema::hasColumn('users', 'referred_by_id')) {
                $table->foreignId('referred_by_id')->nullable()->after('referral_code')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'account_officer_id')) {
                $table->foreignId('account_officer_id')->nullable()->after('referred_by_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'referral_type')) {
                $table->string('referral_type', 30)->nullable()->after('account_officer_id');
            }
            if (!Schema::hasColumn('users', 'terms_accepted_at')) {
                $table->timestamp('terms_accepted_at')->nullable()->after('referral_type');
            }
            if (!Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            }
            if (!Schema::hasColumn('users', 'registration_step')) {
                $table->string('registration_step', 64)->default('step_1_account')->after('phone_verified_at');
            }
            if (!Schema::hasColumn('users', 'current_sub_step')) {
                $table->string('current_sub_step', 64)->nullable()->after('registration_step');
            }
            if (!Schema::hasColumn('users', 'registration_completed')) {
                $table->boolean('registration_completed')->default(false)->after('current_sub_step');
            }
            if (!Schema::hasColumn('users', 'approval_status')) {
                $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('approved')->after('registration_completed');
            }
            if (!Schema::hasColumn('users', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('approval_status')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('users', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approved_at');
            }
        });

        Schema::table('agents', function (Blueprint $table) {
            if (!Schema::hasColumn('agents', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('professionals', function (Blueprint $table) {
            if (!Schema::hasColumn('professionals', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
        });

        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name', 100)->default('mobile-app');
            $table->string('token', 64)->unique();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('channel', ['email', 'phone']);
            $table->string('target', 160);
            $table->string('code_hash');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'channel']);
        });

        Schema::create('user_kyc_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('legal_full_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('id_type', ['nin', 'international_passport', 'drivers_licence', 'voters_card'])->nullable();
            $table->string('id_number', 80)->nullable();
            $table->string('id_front_path')->nullable();
            $table->string('id_back_path')->nullable();
            $table->enum('proof_of_address_type', ['electricity_bill', 'waste_bill', 'bank_statement'])->nullable();
            $table->date('bill_date')->nullable();
            $table->string('proof_of_address_path')->nullable();
            $table->string('passport_photo_path')->nullable();
            $table->string('passport_capture_source', 30)->nullable();
            $table->enum('identity_status', ['incomplete', 'submitted', 'verified', 'rejected'])->default('incomplete');
            $table->timestamp('legal_details_completed_at')->nullable();
            $table->timestamp('government_id_completed_at')->nullable();
            $table->timestamp('proof_of_address_completed_at')->nullable();
            $table->timestamp('passport_photo_completed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('professional_services', function (Blueprint $table) {
            $table->id();
            $table->enum('profession_type', ['lawyer', 'surveyor', 'valuer']);
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('profession_type');
        });

        Schema::create('professional_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('professional_id')->nullable()->constrained('professionals')->nullOnDelete();
            $table->enum('profession_type', ['lawyer', 'surveyor', 'valuer'])->nullable();
            $table->enum('practice_mode', ['individual', 'firm'])->nullable();

            // Lawyer practice fields
            $table->unsignedSmallInteger('year_called_to_bar')->nullable();
            $table->string('nba_branch', 120)->nullable();
            $table->string('supreme_court_enrolment_number', 100)->nullable();
            $table->string('nba_seal_number', 100)->nullable();

            // Surveyor practice fields
            $table->string('surcon_registration_number', 100)->nullable();
            $table->unsignedSmallInteger('year_registered')->nullable();

            // Valuer practice fields
            $table->string('esvarbon_registration_number', 100)->nullable();
            $table->enum('niesv_membership', ['ANIVS', 'FNIVS'])->nullable();

            // Shared regulatory & firm fields
            $table->string('practising_licence_number', 100)->nullable();
            $table->string('firm_name', 160)->nullable();
            $table->string('firm_cac_rc_number', 80)->nullable();

            // Offerings
            $table->json('states_covered')->nullable();
            $table->json('service_modes')->nullable();
            $table->json('languages')->nullable();
            $table->string('typical_turnaround', 100)->nullable();

            // Public profile
            $table->string('display_name', 160)->nullable();
            $table->unsignedTinyInteger('years_exp')->nullable();
            $table->string('main_area', 160)->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_photo_path')->nullable();

            $table->json('completed_sub_steps')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('professional_profile_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_profile_id')->constrained('professional_profiles')->cascadeOnDelete();
            $table->foreignId('professional_service_id')->constrained('professional_services')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['professional_profile_id', 'professional_service_id'], 'prof_profile_service_unique');
        });

        Schema::create('professional_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('professional_profile_id')->nullable()->constrained('professional_profiles')->nullOnDelete();
            $table->enum('entity_type', ['professional', 'agent', 'company'])->default('professional');
            $table->string('document_key', 80);
            $table->string('document_label', 160);
            $table->string('file_path');
            $table->string('original_filename')->nullable();
            $table->string('mime_type', 80)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->timestamps();

            $table->unique(['user_id', 'document_key']);
        });

        Schema::create('referral_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('referral_code_used', 32);
            $table->decimal('commission_rate', 5, 2)->default(5.00);
            $table->unsignedBigInteger('commission_amount')->default(0);
            $table->enum('status', ['pending', 'earned', 'paid', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique('referred_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_commissions');
        Schema::dropIfExists('professional_documents');
        Schema::dropIfExists('professional_profile_service');
        Schema::dropIfExists('professional_profiles');
        Schema::dropIfExists('professional_services');
        Schema::dropIfExists('user_kyc_verifications');
        Schema::dropIfExists('verification_codes');
        Schema::dropIfExists('api_tokens');
    }
};
