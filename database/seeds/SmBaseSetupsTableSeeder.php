<?php

use Illuminate\Database\Seeder;

class SmBaseSetupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        DB::table('sm_base_setups')->insert([
        	[
        		'base_group_id'=>1,
        		'base_setup_name'=>'Male',
        	],
        	[
        		'base_group_id'=>1,
        		'base_setup_name'=>'Female',
        	],
        	[
        		'base_group_id'=>1,
        		'base_setup_name'=>'Others',
        	],


        	[
        		'base_group_id'=>2,
        		'base_setup_name'=>'Islam',
        	],
        	[
        		'base_group_id'=>2,
        		'base_setup_name'=>'Hinduism',
        	],
        	[
        		'base_group_id'=>2,
        		'base_setup_name'=>'Sikhism',
        	],
        	[
        		'base_group_id'=>2,
        		'base_setup_name'=>'Buddhism',
        	],
        	[
        		'base_group_id'=>2,
        		'base_setup_name'=>'Protestantism',
        	],

        	[
        		'base_group_id'=>3,
        		'base_setup_name'=>'A+',
        	],
        	[
        		'base_group_id'=>3,
        		'base_setup_name'=>'O+',
        	],
        	[
        		'base_group_id'=>3,
        		'base_setup_name'=>'B+',
        	],
        	[
        		'base_group_id'=>3,
        		'base_setup_name'=>'AB+',
        	],
        	[
        		'base_group_id'=>3,
        		'base_setup_name'=>'A-',
        	],
        	[
        		'base_group_id'=>3,
        		'base_setup_name'=>'O-',
        	],
        	[
        		'base_group_id'=>3,
        		'base_setup_name'=>'B-',
        	],
        	[
        		'base_group_id'=>3,
        		'base_setup_name'=>'AB-',
        	],
        ]);
    }
}
