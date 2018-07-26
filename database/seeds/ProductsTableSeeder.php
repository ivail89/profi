<?php

use Illuminate\Database\Seeder;
use App\Product;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            [
                'name' => 'Product1',
            ],
            [
                'name' => 'Product2',
            ],
            [
                'name' => 'Product3',
            ]
        ]);
    }
}
