<?php

use Illuminate\Database\Seeder;

class Sm_Class_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
              DB::table('sm_classes')->insert([
                [
                    'class_name' => 'One', 
                ] 
            ]);

    }
}
