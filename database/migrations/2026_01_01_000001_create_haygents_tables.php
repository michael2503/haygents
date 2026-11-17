<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('description');
            $table->string('icon_class');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('state');
            $table->string('neighborhoods');
            $table->string('image_path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('initials', 4);
            $table->string('role_title');
            $table->unsignedInteger('years_exp')->default(1);
            $table->string('city');
            $table->string('state');
            $table->text('description');
            $table->string('credentials');
            $table->enum('verification_status', ['verified', 'pending', 'unverified'])->default('pending');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('avatar_path')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('initials', 4);
            $table->string('specialty');
            $table->string('city');
            $table->string('state');
            $table->text('description');
            $table->string('credentials');
            $table->string('logo_path')->nullable();
            $table->enum('verification_status', ['verified', 'pending', 'unverified'])->default('pending');
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });

        Schema::create('professionals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('initials', 4);
            $table->string('category');
            $table->string('category_slug');
            $table->unsignedInteger('years_exp')->default(1);
            $table->string('city');
            $table->string('state');
            $table->text('description');
            $table->string('credentials');
            $table->string('avatar_path')->nullable();
            $table->enum('verification_status', ['verified', 'pending', 'unverified'])->default('pending');
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });

        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->enum('purpose', ['sale', 'rent', 'lease', 'shortlet']);
            $table->unsignedBigInteger('price');
            $table->string('price_period')->nullable();
            $table->unsignedTinyInteger('bedrooms')->default(0);
            $table->unsignedTinyInteger('bathrooms')->default(0);
            $table->unsignedTinyInteger('parking_spaces')->default(2);
            $table->unsignedInteger('size_sqm')->default(0);
            $table->string('address_area');
            $table->string('city');
            $table->string('state');
            $table->text('description');
            $table->json('features')->nullable();
            $table->string('furnishing')->default('Unfurnished');
            $table->string('image_path');
            $table->boolean('is_featured')->default(false);
            $table->enum('verification_status', ['verified', 'pending', 'unverified'])->default('pending');
            $table->enum('ownership_doc_status', ['verified', 'pending', 'unverified'])->default('pending');
            $table->enum('location_check_status', ['verified', 'pending', 'unverified'])->default('pending');
            $table->enum('title_doc_status', ['verified', 'pending', 'unverified'])->default('pending');
            $table->enum('inspection_status', ['verified', 'pending', 'unverified'])->default('pending');
            $table->enum('legal_doc_status', ['verified', 'pending', 'unverified'])->default('unverified');
            $table->timestamps();
        });

        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'property_id']);
        });

        Schema::create('inspection_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->date('preferred_date');
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('support_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject');
            $table->string('property_ref')->nullable();
            $table->text('message');
            $table->string('status')->default('open');
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_role');
            $table->string('location');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('quote');
            $table->string('avatar_path')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('support_inquiries');
        Schema::dropIfExists('inspection_requests');
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('properties');
        Schema::dropIfExists('professionals');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('agents');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('categories');
    }
};
