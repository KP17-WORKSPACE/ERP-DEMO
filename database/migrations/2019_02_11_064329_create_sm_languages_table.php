<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmLanguage;

class CreateSmLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('language_name')->nullable();
            $table->string('native')->nullable();
            $table->string('language_universal')->nullable();
            $table->tinyInteger('active_status')->default(0);
            $table->integer('created_by')->nullable()->default(1);
            $table->integer('updated_by')->nullable()->default(1);
            $table->integer('lang_id')->nullable()->default(1);
            $table->integer('school_id')->nullable()->default(1);
            $table->timestamps();
        });

        $store = new SmLanguage();
        $store->language_name = 'English';
        $store->native = 'English';
        $store->language_universal = 'en';
        $store->active_status = 1;
        $store->lang_id = 19;
        $store->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_languages');
    }
}
