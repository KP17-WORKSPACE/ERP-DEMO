<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmProductPurchase;
class CreateSmProductPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_product_purchases', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('staff_id');
            $table->date('purchase_date');
            $table->date('expaire_date');
            $table->float('price');
            $table->float('paid_amount');
            $table->float('due_amount');
            $table->string('package');
            $table->integer('school_id')->nullable()->default(1);
            $table->timestamps();
        });

        // $d = new SmProductPurchase();
        // $d->user_id=159;
        // $d->staff_id=31;
        // $d->purchase_date=date('Y-m-d');
        // $d->expaire_date='2022-'.date('m-d');
        // $d->price=200.00;
        // $d->paid_amount=130.50;
        // $d->due_amount=69.50;
        // $d->package='INFIX EDU';
        // $d->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_product_purchases');
    }
}
