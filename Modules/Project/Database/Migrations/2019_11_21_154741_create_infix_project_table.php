<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Project\Entities\InfixProject;
use Faker\Factory as Faker;
use App\SmStaff;

class CreateInfixProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infix_project', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name');
            $table->text('description');
            $table->integer('customer_id')->nullable()->unsigned();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->integer('category_id')->nullable()->unsigned();
            $table->string('photo')->nullable();
            $table->string('team_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('active_status')->default(1);
            $table->integer('is_complete')->default(0);
            $table->timestamps();
        });

        // $customers = SmStaff::where('role_id', 2)->where('active_status', 1)->get();
        // $faker = Faker::create();
        // $count = 1;
        // foreach ($customers as $customer) {
        //     $s = new InfixProject();
        //     $s->code = time();
        //     $s->customer_id = $customer->id;
        //     $s->start_date = date('Y-m-d');
        //     $s->due_date = '2020-12-31';
        //     $s->category_id = 1;
        //     $s->team_id = 1 + $customer->id % 5;
        //     $s->name = $faker->company;
        //     $s->photo = 'public/uploads/projects/' . $count . '.png';
        //     $s->description = $faker->text;
        //     $s->save();
        //     $count++;
        // }
        // foreach ($customers as $customer) {
        //     $s = new InfixProject();
        //     $s->code = time();
        //     $s->customer_id = $customer->id;
        //     $s->start_date = date('Y-m-d');
        //     $s->due_date = '2020-12-31';
        //     $s->category_id = 1;
        //     $s->team_id = 1 + $customer->id % 5;
        //     $s->name = $faker->company;
        //     $s->photo = 'public/uploads/projects/' . $count . '.png';
        //     $s->description = $faker->text;
        //     $s->save();
        //     $count++;
        // }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('infix_project');
    }
}
