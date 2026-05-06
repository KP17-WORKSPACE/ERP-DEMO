<?php

use App\SmExpenseHead;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class SmExpenseHeadTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        for($i=1; $i<=5; $i++){
            $store= new SmExpenseHead();
            $store->name=$faker->word;
            $store->description=$faker->realText($maxNbChars = 100, $indexSize = 1);
            $store->school_id=$faker->ean8;
            $store->active_status=1;
            $store->created_by=$i;
            $store->save();

        }
    }
}
