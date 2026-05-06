<?php

use Illuminate\Database\Seeder;
use App\SmHumanDepartment;
class SmHumanDepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){

        $data = [ 'Production','Purchasing', 'Merchandising', 'Research and Development', 'Marketing', 'Customer Service', 'Accountants', 'Human Resource Management', 'Accounting and Finance' ];
        foreach ($data  as $info) {
          $s = new SmHumanDepartment();
          $s->name= $info;
          $s->save();

        }
         
    }
}
