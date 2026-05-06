<?php

use App\SmStyle;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmStylesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_styles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('style_name', 255)->nullable();
            $table->string('path_main_style', 255)->nullable();
            $table->string('path_infix_style', 255)->nullable();
            $table->string('primary_color', 255)->nullable();
            $table->string('primary_color2', 255)->nullable();
            $table->string('title_color', 255)->nullable();
            $table->string('text_color', 255)->nullable();
            $table->string('white', 255)->nullable();
            $table->string('black', 255)->nullable();
            $table->string('sidebar_bg', 255)->nullable();
            $table->string('barchart1', 255)->nullable();
            $table->string('barchart2', 255)->nullable();
            $table->string('barcharttextcolor', 255)->nullable();
            $table->string('barcharttextfamily', 255)->nullable();
            $table->string('areachartlinecolor1', 255)->nullable();
            $table->string('areachartlinecolor2', 255)->nullable();
            $table->string('dashboardbackground', 255)->nullable();


            $table->tinyInteger('active_status')->default(1);
            $table->tinyInteger('is_active')->default(0);
            $table->timestamps();

            $table->integer('created_by')->nullable()->default(1)->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');

            $table->integer('updated_by')->nullable()->default(1)->unsigned();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('restrict'); 
        });



        $s = new SmStyle();
        $s->style_name = 'Default';
        $s->path_main_style = 'style.css';
        $s->path_infix_style = 'infix.css';
        $s->primary_color = '#415094';
        $s->primary_color2 = '#7c32ff';
        $s->title_color = '#222222';
        $s->text_color = '#828bb2';
        $s->white = '#ffffff';
        $s->black = '#000000';
        $s->sidebar_bg = '#e7ecff';
        $s->barchart1 = '#8a33f8';
        $s->barchart2 = '#f25278';
        $s->barcharttextcolor = '#415094';
        $s->barcharttextfamily = '"poppins", sans-serif';
        $s->areachartlinecolor1 = 'rgba(124, 50, 255, 0.5)';
        $s->areachartlinecolor2 = 'rgba(242, 82, 120, 0.5)';
        $s->dashboardbackground = '';
        $s->is_active = 1;
        $s->save();

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_styles');
    }
}
