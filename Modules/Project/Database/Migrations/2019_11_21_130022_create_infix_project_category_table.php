<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Project\Entities\InfixProjectCategory;
class CreateInfixProjectCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infix_project_category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description');
            $table->tinyInteger('active_status')->default(1);
            $table->integer('created_by')->nullable()->default(1);
            $table->integer('updated_by')->nullable()->default(1);
            $table->timestamps();
        });


            // $category = new InfixProjectCategory();
            // $category->name = 'LMS';
            // $category->description = 'Learning management System';
            // $category->save();

            // $category = new InfixProjectCategory();
            // $category->name = 'CMS';
            // $category->description = 'Content management System';
            // $category->save();
            
            // $category = new InfixProjectCategory();
            // $category->name = 'UMS';
            // $category->description = 'University management System';
            // $category->save();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('infix_project_category');
    }
}
