<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmDesignation;
class CreateSmDesignationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_designations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',255)->nullable(); 
            $table->tinyInteger('active_status')->default(1);
            $table->string('created_by')->nullable()->default(1);
            $table->string('updated_by')->nullable()->default(1);
            $table->timestamps();
        });
 

        $data = ['Accounts Manager', 'Recruitment Manager', 'Technology Manager', 'Store Manager', 'Departmental Managers', 'General Managers', 'Chief Information Officer (CIO)', 'Chief Technology Officer (CTO)'];
        foreach ($data as $info) {
            $s = new SmDesignation();
            $s->title = $info;
            $s->save();

        }


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_designations');
    }
}
