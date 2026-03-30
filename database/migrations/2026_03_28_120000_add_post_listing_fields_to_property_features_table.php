<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_features', function (Blueprint $table) {
            $table->string('view')->nullable()->after('built_year');
            $table->string('other_main_features')->nullable()->after('view');
            $table->string('floors')->nullable()->after('other_main_features');
            $table->string('other_rooms')->nullable()->after('floors');

            $table->text('other_business_communication')->nullable()->after('other_rooms');
            $table->boolean('broadband_internet_access')->default(false)->after('other_business_communication');
            $table->boolean('satellite_or_cable_tv_ready')->default(false)->after('broadband_internet_access');
            $table->boolean('intercom')->default(false)->after('satellite_or_cable_tv_ready');

            $table->text('other_community_facilities')->nullable()->after('intercom');
            $table->boolean('community_lawn_or_garden')->default(false)->after('other_community_facilities');
            $table->boolean('community_swimming_pool')->default(false)->after('community_lawn_or_garden');
            $table->boolean('community_gym')->default(false)->after('community_swimming_pool');
            $table->boolean('first_aid_or_medical_centre')->default(false)->after('community_gym');
            $table->boolean('day_care_centre')->default(false)->after('first_aid_or_medical_centre');
            $table->boolean('kids_play_area')->default(false)->after('day_care_centre');
            $table->boolean('barbecue_area')->default(false)->after('kids_play_area');
            $table->boolean('mosque')->default(false)->after('barbecue_area');
            $table->boolean('community_centre')->default(false)->after('mosque');

            $table->text('other_healthcare_recreation')->nullable()->after('community_centre');
            $table->boolean('lawn_or_garden')->default(false)->after('other_healthcare_recreation');
            $table->boolean('swimming_pool')->default(false)->after('lawn_or_garden');
            $table->boolean('sauna')->default(false)->after('swimming_pool');
            $table->boolean('jacuzzi')->default(false)->after('sauna');

            $table->string('other_nearby_places')->nullable()->after('nearby_public_transport');

            $table->text('other_facilities')->nullable()->after('other_nearby_places');
            $table->boolean('maintenance_staff')->default(false)->after('other_facilities');
            $table->boolean('security_staff')->default(false)->after('maintenance_staff');
            $table->boolean('facilities_for_disabled')->default(false)->after('security_staff');
        });
    }

    public function down(): void
    {
        Schema::table('property_features', function (Blueprint $table) {
            $table->dropColumn([
                'view',
                'other_main_features',
                'floors',
                'other_rooms',
                'other_business_communication',
                'broadband_internet_access',
                'satellite_or_cable_tv_ready',
                'intercom',
                'other_community_facilities',
                'community_lawn_or_garden',
                'community_swimming_pool',
                'community_gym',
                'first_aid_or_medical_centre',
                'day_care_centre',
                'kids_play_area',
                'barbecue_area',
                'mosque',
                'community_centre',
                'other_healthcare_recreation',
                'lawn_or_garden',
                'swimming_pool',
                'sauna',
                'jacuzzi',
                'other_nearby_places',
                'other_facilities',
                'maintenance_staff',
                'security_staff',
                'facilities_for_disabled',
            ]);
        });
    }
};