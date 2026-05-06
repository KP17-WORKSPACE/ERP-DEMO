<?php

use App\User;
use App\SmStaff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('role_id')->nullable();
            $table->string('full_name', 50)->nullable();
            $table->string('username', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('password', 100)->nullable();
            $table->string('usertype', 10)->nullable();
            $table->tinyInteger('access_status')->default(1)->comment('0 = off, 1 = on');
            $table->tinyInteger('active_status')->default(1);
            $table->string('random_code')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });


        $user = User::find(1);
        if (empty($user)) {
            $user = new User();
        }
        $user->role_id = 1;
        $user->full_name = 'Super Admin';
        $user->email = 'admin@demo.com';
        $user->username = 'admin@demo.com';
        $user->password = Hash::make('123456');
        $user->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
