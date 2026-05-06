<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SmRole::class);
        $this->call(Sm_users_seeder::class);
        $this->call(SmStaffsTableSeeder::class);
        $this->call(SmHumanDepartmentsTableSeeder::class);
        $this->call(SmBaseSetupsTableSeeder::class);
        $this->call(SmIncomeHeadTableSeeder::class);
        $this->call(SmquestionLevelTableSeeder::class);
        $this->call(SmquestionGroupTableSeeder::class);
        $this->call(SmQuestionBanksTableSeeder::class);
        $this->call(SmHourlyRateTableSeeder::class);
        $this->call(SmBankAccountTableSeeder::class);
        $this->call(SmExpenseHeadTableSeeder::class);
        $this->call(SmHrPayrollGenerateTableSeeder::class);
        $this->call(SmTicketPrioritySeeder::class);
        $this->call(SmTicketCategorySeeder::class);
        $this->call(SmTicketSeeder::class);
        $this->call(SmLeaveTypesTableSeeder::class);
    }
}
