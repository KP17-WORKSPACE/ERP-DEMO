<?php
 
 namespace App\Console\Commands;

use App\SysHelper;
use Illuminate\Console\Command;
 use DB;
 class CustomCommand extends Command
 {
     /**
      * The name and signature of the console command.
      *
      * @var string
      */
     protected $signature = 'custom:command';
     /**
      * The console command description.
      *
      * @var string
      */
     protected $description = 'In all inactive users';
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
        try{
            
            $insert_data[] = array( 'last_updated_date' => date('Y-m-d H:i:s'), 'msg' => 'done');
            DB::table('cron_test')->insert($insert_data);

            SysHelper::LeadWeeklyNotificationMail();
        }
        catch(\Exception $e){
            $insert_data[] = array( 'last_updated_date' => date('Y-m-d H:i:s'), 'msg' => $e);
            DB::table('cron_test')->insert($insert_data);            
        }

         //DB::table('users')->where('active', 0)->delete();
         //$this->info('All inactive users are deleted successfully!');
     }
 }