<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\SmEnlistedSupplier;
use Faker\Factory as Faker;
class CreateSmEnlistedSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_enlisted_suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_name',100)->nullable();
            $table->string('company_address',500)->nullable();
            $table->string('contact_person_name',255)->nullable();
            $table->string('contact_person_mobile',255)->nullable();
            $table->string('contact_person_email',100)->nullable();
            $table->string('cotact_person_address',500)->nullable();
            $table->string('total_quantity',500)->nullable();
            $table->string('description',500)->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->string('created_by')->nullable()->default(1);
            $table->string('updated_by')->nullable()->default(1); 
            $table->timestamps();
        });

        // $faker = Faker::create();
        // for($i=1; $i<=5; $i++){
        //     $s = new SmEnlistedSupplier();
        //     $s->company_name = 'company name '. $i; 
        //     $s->company_address = 'address '. $i; 
        //     $s->contact_person_name ='Contact Person Name '. $i; 
        //     $s->contact_person_mobile = '019782364923'.$i; 
        //     $s->contact_person_email =$faker->email; 
        //     $s->description = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.'; 
        //     $s->save();
        // }


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_enlisted_suppliers');
    }
}
