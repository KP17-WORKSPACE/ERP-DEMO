<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Ticket;
class SmTicketSeeder extends Seeder
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
            $store= new Ticket();
            $store->user_id=$i+1;
            $store->category_id=$i;
            $store->description=$faker->realText($maxNbChars = 200, $indexSize = 1);
            $store->subject=$faker->sentence;
            $store->priority_id=$i;
            $store->save();

        }
    }
}
