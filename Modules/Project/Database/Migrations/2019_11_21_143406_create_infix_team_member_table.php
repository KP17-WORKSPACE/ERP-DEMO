<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Project\Entities\InfixTeamMember;

class CreateInfixTeamMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infix_team_member', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('staff_id')->nullable()->unsigned(); 
            $table->integer('team_id')->nullable()->unsigned(); 
            $table->tinyInteger('active_status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });


        // for($j=1; $j<=5; $j++){
        //     for($i=1; $i<=$j; $i++){
        //         $s= new InfixTeamMember();
        //         $s->staff_id =$i;
        //         $s->team_id =$j; 
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
        Schema::dropIfExists('infix_team_member');
    }
}
