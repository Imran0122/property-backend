<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\AgentPackage;
use App\Models\Property;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    
    protected function schedule(Schedule $schedule): void
    {
        /**
         * 1️⃣ Expire agent packages (daily)
         */
        $schedule->call(function () {
            AgentPackage::where('status', 'active')
                ->whereNotNull('expires_at')
                ->where('expires_at', '<', now())
                ->update([
                    'status' => 'expired'
                ]);
        })->daily();

        /**
         * 2️⃣ Auto un-feature expired properties (hourly)
         */
        $schedule->call(function () {
            Property::where('is_featured', 1)
                ->whereNotNull('featured_until')
                ->where('featured_until', '<', now())
                ->update([
                    'is_featured' => 0,
                    'featured_until' => null
                ]);
        })->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
