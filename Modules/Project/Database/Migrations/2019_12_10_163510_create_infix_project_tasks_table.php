<?php

use App\SmStaff;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Modules\Project\Entities\InfixProject;
use Illuminate\Database\Migrations\Migration;
use Modules\Project\Entities\InfixProjectTask;

class CreateInfixProjectTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infix_project_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',255)->nullable();
            $table->text('description')->nullable();
            $table->integer('project_id');
            $table->tinyInteger('is_active')->default(1);
            $table->tinyInteger('is_complete')->default(0);
            $table->tinyInteger('active_status')->default(1);
            $table->string('image')->nullable();
            $table->string('due_date')->nullable();
            $table->integer('created_by')->nullable()->default(1);
            $table->integer('updated_by')->nullable()->default(1);
            $table->integer('assigned_to')->nullable();
            $table->integer('completed_by')->nullable();
            $table->timestamps();
        });
        $faker = Faker::create();

        // $projects= InfixProject::all(); 
        // foreach($projects as $project){
        //     $team_members=DB::table('infix_team_member')->where('team_id', $project->team_id)->get();
        //     foreach($team_members as $team_member){
        //         $s= new InfixProjectTask();
        //         $s->title = $faker->text ;
        //         $s->description =  $faker->text ;
        //         $s->project_id = $project->id;
        //         $s->assigned_to = $team_member->staff_id;
        //         $s->is_complete = $team_member->id%2;
        //         $s->due_date = date('Y-m-d');
        //         $s->save();
        //     } 
        // } 

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('infix_project_tasks');
    }
}
