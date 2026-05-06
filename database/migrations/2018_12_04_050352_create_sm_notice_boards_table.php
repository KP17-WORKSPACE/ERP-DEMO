<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmNoticeBoardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_notice_boards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('notice_title',255)->nullable();
            $table->text('notice_message')->nullable();
            $table->date('notice_date')->nullable();
            $table->date('publish_on')->nullable();
            $table->string('inform_to',100)->nullable()->comment('Notice message sent to these roles');
            $table->tinyInteger('active_status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('school_id')->nullable()->default(1);
            $table->timestamps();
        });
        // DB::table('sm_notice_boards')->insert([
        //     [
        //         'notice_title' => 'Supply of Disc Insulator with Fittings for 11 KV Line.',
        //         'notice_message' => 'Supply of Disc Insulator with Fittings for 11 KV Line.',
        //         'notice_date' => '2019-06-11',
        //         'publish_on' => '2019-06-12',
        //         'inform_to' => '1,3,4,5',
        //     ],
        //     [
        //         'notice_title' => 'Notice For the Issuance of Bid Documents For Selection of the Project',
        //         'notice_message' => 'Notice For the Issuance of Bid Documents For Selection of the Project Sponsors For Implementation of a 5 MW+-20% Grid Connected Waste to Power Project on Build, Own and Operate (BOO) Basis.',
        //         'notice_date' => '2019-06-10',
        //         'publish_on' => '2019-06-11',
        //         'inform_to' => '1,3,4,5',
        //     ],
        //     [
        //         'notice_title' => 'Purchase of Grass Cutter Machine Backpack, Rechargeable Plant Trimmer (with Lithium Battery) and Electric Grass Cutter Machine.',
        //         'notice_message' => 'Purchase of Grass Cutter Machine Backpack, Rechargeable Plant Trimmer (with Lithium Battery) and Electric Grass Cutter Machine.',
        //         'notice_date' => '2019-06-10',
        //         'publish_on' => '2019-06-11',
        //         'inform_to' => '1,3,4,5',
        //     ],
        //     [
        //         'notice_title' => 'Supply and installation of farm machinery and similar equipment under DPP',
        //         'notice_message' => 'Supply and installation of farm machinery and similar equipment under DPP line item of Procurement of Scientific equipment and farm machineries.',
        //         'notice_date' => '2019-06-10',
        //         'publish_on' => '2019-06-11',
        //         'inform_to' => '1,3,4,5',
        //     ],
        //     ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_notice_boards');
    }
}
