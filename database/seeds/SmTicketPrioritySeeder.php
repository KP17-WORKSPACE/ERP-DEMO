<?php

use Illuminate\Database\Seeder;
use App\Priority;
class SmTicketPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [ 'Normal','Low','Critical','Urgent' ];
        foreach ($data  as $info) {
          $s = new Priority();
          $s->name= $info;
          $s->save();

        }
    }
}
