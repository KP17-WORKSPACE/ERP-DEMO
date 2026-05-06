<?php

use App\User;
use App\SmStaff;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmStaffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_staffs', function (Blueprint $table) {
            $table->increments('id'); 
            $table->integer('user_id')->nullable()->default(1);
            $table->integer('role_id')->nullable()->default(1);
            $table->string('staff_no')->nullable();
            $table->integer('designation_id')->nullable()->unsigned();
            $table->integer('department_id')->nullable()->unsigned();
            $table->string('company_name',255)->nullable();
            $table->string('first_name',100)->nullable();
            $table->string('last_name',100)->nullable();
            $table->string('full_name',200)->nullable();
            $table->string('fathers_name',100)->nullable();
            $table->string('mothers_name',100)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('date_of_joining')->nullable();
            $table->integer('gender_id')->nullable()->unsigned()->default(1);
            $table->string('email',50)->nullable();
            $table->string('mobile',50)->nullable();
            $table->string('emergency_mobile',50)->nullable();
            $table->string('marital_status',30)->nullable();
            $table->string('merital_status',30)->nullable();
            $table->string('staff_photo')->nullable();
            $table->string('current_address',500)->nullable();
            $table->string('permanent_address',500)->nullable();
            $table->string('qualification',200)->nullable();
            $table->string('experience',200)->nullable();
            $table->string('epf_no',100)->nullable();
            $table->string('basic_salary',200)->nullable();
            $table->string('contract_type',200)->nullable();
            $table->string('location',50)->nullable();
            $table->string('casual_leave',15)->nullable();
            $table->string('medical_leave',15)->nullable();
            $table->string('metarnity_leave',15)->nullable();


            /* *************** Bank Details *************** */
            $table->string('bank_account_name',100)->nullable();
            $table->string('bank_account_no',100)->nullable();
            $table->string('bank_name',100)->nullable();
            $table->string('bank_brach',100)->nullable();
            /* *************** Bank Details *************** */



            /* *************** Online Payment *************** */
            $table->string('paypal_account',255)->nullable();
            $table->string('payoneer_account',255)->nullable();
            $table->string('skrill_account',255)->nullable();
            $table->string('stripe_account',255)->nullable();
            $table->string('wepay_account',255)->nullable();
            $table->string('amazon_account',255)->nullable();
            /* *************** Online Payment *************** */



            /* *************** Social Url *************** */
            $table->string('facebook_url',100)->nullable();
            $table->string('twiteer_url',100)->nullable();
            $table->string('linkedin_url',100)->nullable();
            $table->string('instragram_url',100)->nullable();
            /* *************** Social Url *************** */


            $table->string('joining_letter',500)->nullable();
            $table->string('resume',500)->nullable();
            $table->string('other_document',500)->nullable(); 
            $table->string('notes',500)->nullable();
            $table->tinyInteger('active_status')->default(1)->comment('0 = inactive, 1 = active');
            $table->tinyInteger('delete_status')->default(1)->comment('0 = yes, 1 = no');

            $table->string('driving_license',255)->nullable();
            $table->date('driving_license_ex_date')->nullable();

            $table->tinyInteger('created_by')->nullable();
            $table->tinyInteger('updated_by')->nullable();
            $table->timestamps();
        });



        $staff = SmStaff::find(1);
        if(empty($staff)){
             $staff = new SmStaff();
         } 
         $staff->user_id  = 1;
         $staff->role_id  = 1;
         $staff->staff_no  = 1;
         $staff->designation_id  = 1;
         $staff->department_id  = 1; 
         $staff->first_name  = 'Super'; 
         $staff->last_name  = 'Admin'; 
         $staff->full_name  = 'Super Admin'; 
         $staff->gender_id  = 1; 
         $staff->email  = 'admin@demo.com'; 
         $staff->staff_photo  = 'public/uploads/peoples/1.jpg'; 
         $staff->save();



            // $faker = Faker::create();    
            // for($j=2; $j<=3; $j++){   
            //     for($i=2; $i<11; $i++){   
            //         $first_name  = $faker->firstName($gender =  'male');
            //         $last_name   = $faker->lastName($gender =  'male'); 
            //         $staff_email = strtolower($first_name.'_'.$last_name.'@demo.com');  

            //         //insert staff user & pass
            //         $newUser            = new User();
            //         $newUser->role_id   =$j;
            //         $newUser->full_name =$first_name.' '.$last_name;
            //         $newUser->email     =$staff_email;
            //         $newUser->username  =$staff_email;
            //         $newUser->password  = Hash::make(123456);
            //         $newUser->save();
            //         $newUser->toArray();
            //         $staff_id_number=$newUser->id;   

            //         DB::table('sm_staffs')->insert([
            //          [
            //              'user_id'          =>$staff_id_number,
            //              'role_id'          =>$j,
            //              'staff_no'         =>$i*$j,  
            //              'first_name'       =>$first_name, 
            //              'last_name'        =>$last_name, 
            //              'full_name'        =>$first_name.' '.$last_name, 
            //              'fathers_name'     =>$faker->firstName($gender =  'male'), 
            //              'mothers_name'     =>$faker->firstName($gender =  'female'),   
            //              'email'            =>$staff_email,  
            //              'staff_photo'      =>'public/uploads/peoples/'.$i.'.jpg',
            //              'current_address'  => $faker->address,  
            //              'paypal_account'=>'demo@paypal.com', 
            //              'payoneer_account'=>'demo@payoneer.com', 
            //              'skrill_account'=>'demo@skrill.com',  
            //              'bank_account_name'=>$first_name.' '. $last_name, 
            //              'bank_account_no'=>'456456456345789', 
            //              'bank_name'=>'DBBL', 
            //          ]  
            //         ]); 
            //     }
            // } 



 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_staffs');
    }
}
