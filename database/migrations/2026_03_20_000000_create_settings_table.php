<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // General Settings
            $table->string('site_name')->default('Property Site');
            $table->string('support_email')->default('support@propertysite.com');
            $table->string('currency')->default('PKR');
            $table->string('country')->default('Pakistan');
            $table->string('website_url')->nullable();
            $table->text('site_description')->nullable();

            // Listing Rules
            $table->boolean('auto_approve_verified_agents')->default(false);
            $table->boolean('require_image_validation')->default(true);
            $table->boolean('enable_duplicate_listing_detection')->default(true);
            $table->boolean('manual_approval_featured_listings')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};