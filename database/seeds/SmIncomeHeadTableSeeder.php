<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\SmIncomeHead;
class SmIncomeHeadTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        for($i=1; $i<=4; $i++){
            $store= new SmIncomeHead();
            $store->name=$faker->word;
            $store->description=$faker->realText($maxNbChars = 200, $indexSize = 1);
            $store->school_id=$faker->randomDigit;
            $store->active_status=1;
            $store->created_by=$i;
            $store->save();

        }
    }
}
