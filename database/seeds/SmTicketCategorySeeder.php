<?php

use Illuminate\Database\Seeder;
use App\Category;
class SmTicketCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [ 'Production','Purchasing', 'Merchandising', 'Research and Development', 'Marketing', 'Customer Service', 'Accountants', 'Human Resource Management', 'Accounting and Finance' ];
        foreach ($data  as $info) {
          $s = new Category();
          $s->name= $info;
          $s->save();

        }
    }
}
