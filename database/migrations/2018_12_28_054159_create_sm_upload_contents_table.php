<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmUploadContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_upload_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('content_title')->length(100)->nullable();
            $table->integer('content_type')->nullable();
            $table->integer('available_for_role')->nullable();
            $table->integer('available_for_class')->nullable();
            $table->integer('available_for_section')->nullable();
            $table->date('upload_date')->nullable();
            $table->string('description')->length(500)->nullable();
            $table->string('upload_file')->length(200)->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('school_id')->nullable()->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_upload_contents');
    }
}
