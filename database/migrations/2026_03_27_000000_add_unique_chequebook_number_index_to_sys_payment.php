<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueChequebookNumberIndexToSysPayment extends Migration
{
    public function up()
    {
        Schema::table('sys_payment', function (Blueprint $table) {
            if (!Schema::hasColumn('sys_payment', 'chequebook_id') || !Schema::hasColumn('sys_payment', 'cheque_number')) {
                return;
            }

            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = array_map('strtolower', array_keys($sm->listTableIndexes('sys_payment')));
            if (!in_array('sys_payment_chequebook_cheque_unique', $indexes)) {
                $table->unique(['chequebook_id', 'cheque_number'], 'sys_payment_chequebook_cheque_unique');
            }
        });
    }

    public function down()
    {
        Schema::table('sys_payment', function (Blueprint $table) {
            $table->dropUnique('sys_payment_chequebook_cheque_unique');
        });
    }
}
