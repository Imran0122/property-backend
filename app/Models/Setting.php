<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'site_name',
        'support_email',
        'currency',
        'country',
        'website_url',
        'site_description',
        'auto_approve_verified_agents',
        'require_image_validation',
        'enable_duplicate_listing_detection',
        'manual_approval_featured_listings',
    ];

    protected $casts = [
        'auto_approve_verified_agents' => 'boolean',
        'require_image_validation' => 'boolean',
        'enable_duplicate_listing_detection' => 'boolean',
        'manual_approval_featured_listings' => 'boolean',
    ];
}