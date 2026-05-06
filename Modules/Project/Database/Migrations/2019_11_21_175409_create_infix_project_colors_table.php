<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Project\Entities\InfixProjectColor;

class CreateInfixProjectColorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infix_project_colors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('color_code', 255);
            $table->string('is_active')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });

        // $color = new InfixProjectColor();
        // $color->name = 'Orange';
        // $color->color_code = '#ff5050';
        // $color->created_by = '1';
        // $color->updated_by = '1';
        // $color->save();

        // $color = new InfixProjectColor();
        // $color->name = 'Green';
        // $color->color_code = '#4BA90A';
        // $color->created_by = '1';
        // $color->updated_by = '1';
        // $color->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('infix_project_colors');
    }
}
