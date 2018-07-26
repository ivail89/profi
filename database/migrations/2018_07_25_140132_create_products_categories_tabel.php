<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsCategoriesTabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::dropIfExists('products_categories');
        Schema::create('products_categories', function (Blueprint $table){
            $table->increments('id');

            //создание поля для связывания с таблицей categories
            $table->integer('category_id')->unsigned()->default (1);
            //создание внешнего ключа для поля 'category_id', который связан с полем id таблицы 'categories'
            $table->foreign('category_id')->references('id')->on('categories');

            $table->integer('product_id')->unsigned()->default (1);
            $table->foreign('product_id')->references('id')->on('products');
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('products_categories');
    }
}
