<?php

use Illuminate\Database\Seeder;
use App\ProductCategory;

class ProductsCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products_categories')->insert([
            [
                'category_id' => '1',
                'product_id' => '1',
            ],
            [
                'category_id' => '1',
                'product_id' => '2',
            ],
            [
                'category_id' => '2',
                'product_id' => '1',
            ],
            [
                'category_id' => '1',
                'product_id' => '3',
            ]
        ]);
    }
}
