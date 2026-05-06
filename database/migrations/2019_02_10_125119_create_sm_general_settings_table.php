<?php

use App\SmGeneralSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmGeneralSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_general_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_name')->nullable();
            $table->string('site_title')->nullable();
            $table->integer('school_id')->nullable()->default(1);
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->integer('session_id')->nullable()->default(1);
            $table->integer('language_id')->nullable()->default(1);
            $table->integer('date_format_id')->nullable()->default(1);
            $table->integer('time_zone_id')->nullable()->default(1);
            $table->string('currency')->nullable()->default('USD');
            $table->string('currency_symbol')->nullable()->default('$');
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('system_version')->nullable()->default('1.0');
            $table->integer('active_status')->nullable()->default(1);
            $table->string('currency_code')->nullable()->default('USD');
            $table->string('language_name')->nullable()->default('en');
            $table->string('session_year')->nullable()->default('2020');
            $table->string('system_purchase_code')->nullable();
            $table->date('system_activated_date')->nullable();
            $table->string('envato_user')->nullable();
            $table->string('envato_item_id')->nullable();

            $table->integer('ttl_rtl')->default(2);
            $table->string('system_domain')->nullable();
            $table->string('copyright_text')->nullable();
            $table->timestamps();
        });


        DB::table('sm_general_settings')->insert([

            [
                'company_name' => 'Infix Business ERP',
                'site_title' => 'Infix Business ERP',
                'address' => '89/2 Panthapath, Dhanmondi 1215 Dhaka, Bangladesh',
                'phone' => '+8801841-136251',
                'email' => 'info@spondonit.com',
                'language_id' => 1,
                'system_purchase_code' => '',
                'envato_user' => '',
                'envato_item_id' => '',
                'system_domain' => '',
                'copyright_text' => 'Copyright &copy; 2020 All rights reserved | This template is made  by Codethemes',
                'logo' => 'public/uploads/settings/logo.png',
                'favicon' => 'public/uploads/settings/favicon.png',
                'currency' => 'USD',
                'time_zone_id' => '379',
                

            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_general_settings');
    }
}
