<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmHomePageSetting;
class CreateSmHomePageSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 

        Schema::create('sm_home_page_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',255)->nullable();
            $table->string('long_title',255)->nullable();
            $table->text('short_description')->nullable();
            $table->string('link_label',255)->nullable();
            $table->string('link_url',255)->nullable();
            $table->string('image',255)->nullable();
            $table->timestamps();
        });

        $s = new SmHomePageSetting();
        $s->title = 'THE ULTIMATE BUSINESS ERP';
        $s->long_title = 'INFIX';
        $s->short_description = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        $s->link_label = 'Learn More About Us';
        $s->link_url = 'http://infixedu.com/about';
        $s->image = 'public/backEnd/img/client/home-banner1.jpg';
        $s->save();


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_home_page_settings');
    }
}
