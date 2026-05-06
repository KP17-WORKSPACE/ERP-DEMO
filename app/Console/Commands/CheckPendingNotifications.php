<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\SystemNotification;
use Carbon\Carbon;




class CheckPendingNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for pending notifications and process them';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dubaiTime = Carbon::now('Asia/Dubai');
        
        // Find notifications that were created at least 15 minutes ago and haven't been shown yet
        $notifications = SystemNotification::where('is_resolved', false)
            ->where('is_shown', false)
            ->where('created_at', '<=', $dubaiTime->copy()->subMinutes(15))
            ->get();

        foreach ($notifications as $notification) {
            // Show the notification
            $notification->is_shown = true;
            $notification->save();
        }

        $this->info('Processed ' . $notifications->count() . ' notifications');
    }
}
