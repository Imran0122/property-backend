<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Property;

class ExpireFeaturedProperties extends Command
{
    protected $signature = 'properties:expire-featured';
    protected $description = 'Expire featured properties';

    public function handle()
    {
        Property::where('is_featured', 1)
            ->whereNotNull('featured_until')
            ->where('featured_until', '<', now())
            ->update([
                'is_featured' => 0,
                'featured_until' => null
            ]);

        $this->info('Expired featured properties');
    }
}
