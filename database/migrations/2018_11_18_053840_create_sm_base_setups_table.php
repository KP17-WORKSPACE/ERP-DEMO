<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmBaseSetupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_base_setups', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('base_group_id');
            $table->string('base_setup_name', 100);
            $table->integer('school_id')->nullable()->default(1);
            $table->tinyInteger('active_status')->default(1);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });

        DB::table('sm_base_setups')->insert([
            [
                'base_group_id' => 1,
                'base_setup_name' => 'Male',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'base_group_id' => 1,
                'base_setup_name' => 'Female',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'base_group_id' => 1,
                'base_setup_name' => 'Others',
                'created_at' => date('Y-m-d h:i:s'),
            ],


            [
                'base_group_id' => 2,
                'base_setup_name' => 'Islam',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'base_group_id' => 2,
                'base_setup_name' => 'Hinduism',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'base_group_id' => 2,
                'base_setup_name' => 'Sikhism',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'base_group_id' => 2,
                'base_setup_name' => 'Buddhism',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'base_group_id' => 2,
                'base_setup_name' => 'Protestantism',
                'created_at' => date('Y-m-d h:i:s'),
            ],

            [
                'base_group_id' => 3,
                'base_setup_name' => 'A+',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'base_group_id' => 3,
                'base_setup_name' => 'O+',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'base_group_id' => 3,
                'base_setup_name' => 'B+',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'base_group_id' => 3,
                'base_setup_name' => 'AB+',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'base_group_id' => 3,
                'base_setup_name' => 'A-',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'base_group_id' => 3,
                'base_setup_name' => 'O-',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'base_group_id' => 3,
                'base_setup_name' => 'B-',
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'base_group_id' => 3,
                'base_setup_name' => 'AB-',
                'created_at' => date('Y-m-d h:i:s'),
            ],
        ]);
    }







    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_base_setups');
    }
}
