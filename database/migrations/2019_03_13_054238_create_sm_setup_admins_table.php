<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmSetupAdmin;
class CreateSmSetupAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_setup_admins', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type')->nullable()->comment('1 purpose, 2 complaint type, 3 source, 4 Reference');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->integer('created_by')->nullable()->default(1);
            $table->integer('updated_by')->nullable()->default(1); 
            $table->timestamps();
        });



  
        $dataArr= [
            [1, 3, 'Lorem Ipsum is simply dummy text ', 'Lorem Ipsum is simply dummy text '],
            [2, 3, 'Lorem Ipsum is simply dummy text ', 'Lorem Ipsum is simply dummy text '],
            [3, 3, 'Lorem Ipsum is simply dummy text ', 'Lorem Ipsum is simply dummy text '],
            [4, 1, 'Lorem Ipsum is simply dummy text ', 'Lorem Ipsum is simply dummy text '],
            [5, 1, 'Lorem Ipsum is simply dummy text ', 'Lorem Ipsum is simply dummy text '],
            [6, 1, 'Lorem Ipsum is simply dummy text ', 'Lorem Ipsum is simply dummy text '],
            [7, 1, 'Lorem Ipsum is simply dummy text ', 'Lorem Ipsum is simply dummy text '],
            [8, 1, 'Lorem Ipsum is simply dummy text ', 'Lorem Ipsum is simply dummy text '],
            [9, 1, 'Lorem Ipsum is simply dummy text ', 'YLorem Ipsum is simply dummy text '],
            [10, 1, 'Lorem Ipsum is simply dummy text ', 'Lorem Ipsum is simply dummy text '], 
            [11, 2, 'Lorem Ipsum is simply dummy text ', 'Lorem Ipsum is simply dummy text '],
            [12, 2, 'Lorem Ipsum is simply dummy text ', 'Lorem Ipsum is simply dummy text '],
            [13, 2, 'Lorem Ipsum is simply dummy text ', 'Lorem Ipsum is simply dummy text '],
            [14, 2, 'Lorem Ipsum is simply dummy text ', 'Lorem Ipsum is simply dummy text '],
            [15, 2, 'Lorem Ipsum is simply dummy text ', 'Lorem Ipsum is simply dummy text '],
            [16, 2, 'Lorem Ipsum is simply dummy text ', 'Lorem Ipsum is simply dummy text '],
            [17, 2, 'Lorem Ipsum is simply dummy text ', 'Lorem Ipsum is simply dummy text '],
            [18, 4, 'Lorem Ipsum is simply dummy text ', 'Lorem Ipsum is simply dummy text '],
            [19, 4, 'Lorem Ipsum is simply dummy text ', 'Lorem Ipsum is simply dummy text '],
            [20, 4, 'Lorem Ipsum is simply dummy text ', 'Lorem Ipsum is simply dummy text ']
        ];
        foreach ($dataArr as $data) { 
            $store = new SmSetupAdmin();
            $store->id                  =  $data[0];
            $store->type                =  $data[1];
            $store->name                =  $data[2];
            $store->description         =  $data[3]; 
            $store->save();
        }




    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_setup_admins');
    }
}
