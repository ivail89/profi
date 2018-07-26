<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Product;
use App\Category;
use App\ProductCategory;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(){
        $products = Product::all();
        return view('product',['products' => $products]);
    }

    // Обновление данных на основании запроса из формы редактирования
    public function update(){
        switch ($_POST['type']){
            case 'ADD':
                /*
                 * Для добавления необходимо указать Имя нового продукта и ID категории товара.
                 */

                //Данная Категория уже существует?
                $addCategory = Category::with('products')
                        ->where('id', $_POST['category_id'])
                        ->get();
                if (!$addCategory->count()){
                    echo 'category_id '.$_POST['category_id'].' - do not exists!';
                    $products = Product::all();
                    return view('product',['products' => $products]);
                }

                //Данный Продукт уже существует? 
                $addProduct = Product::with('categories')
                    ->where('name', $_POST['product'])
                    ->get();
                if ($addProduct->count()){
                    echo 'Product '.$_POST['product'].' - exists!';
                    $products = Product::all();
                    return view('product',['products' => $products]);
                }
                
                // Вставка записей в БД
                $addProduct = new Product(['name' => $_POST['product']]);
                foreach ($addCategory as $aC){
                    $aC->products()->save($addProduct);
                }
                //Обновляем страницу с новыми данными
                $products = Product::all();
                return view('product',['products' => $products]);
            case 'EDIT':
                /*
                 * Для редактрирования необходимо указать ID редактируемой Категории и новое имя Продукта.
                 * Порядок обработки запроса:
                 * 1. Продукт с таким ID существует? Нет - ошибка
                 * 2. Проукт существует, отличается имя обновляем имя Продукта
                 * 3. Имя совпадает, ID Категории добавляем к имеющейся Категории 
                 */
                
                //Данная Категория уже существует?
                $editCategory = Category::with('products')
                        ->where('id', $_POST['category_id'])
                        ->get();
                if (!$editCategory->count()){
                    echo 'category_id '.$_POST['category_id'].' - do not exists!';
                    $products = Product::all();
                    return view('product',['products' => $products]);
                }
                
                //ID данного продукта существует?
                $editID = Product::with('categories')
                    ->where('id', $_POST['id'])
                    ->get();
                if (!$editID->count()){
                    echo 'Product ID '.$_POST['id'].' - do not exists!';
                    $products = Product::all();
                    return view('product',['products' => $products]);
                }
                
                //Данный Продукт (Имя) уже существует? 
                $editProduct = Product::with('categories')
                    ->where('name', $_POST['product'])
                    ->get();
                if ($editProduct->count()){
                    foreach ($editProduct as $aP){
                        $aP -> categories() -> attach($_POST['category_id']);
                    }
                    $products = Product::all();
                    return view('product',['products' => $products]);
                } else {
                    Product::where('id', '=', $_POST['id'])
                        ->update(['name' => $_POST['product']]);
                    $products = Product::all();
                    return view('product',['products' => $products]);
                }
            case 'DELETE':
                /*
                 * Для удаления достаточно указать ID удаляемого Продукта
                 * Выполняем проверку - существует ли Продукт с таким ID
                 * Все связи с данным продуктом удаляем из таблицы products_categories
                 */
                if (!is_int($_POST['id'])){
                    echo 'id must have int type';
                    //Обновляем страницу с новыми данными
                    $products = Product::all();
                    return view('product',['products' => $products]);                    
                } 
                $deleteProduct = Product::with('categories')
                    ->where('id', $_POST['id'])
                    ->get();
                if ($deleteProduct->count()){
                    foreach ($deleteProduct as $dP){
                        // получаем экземпляры Категорий, связь с которыми нужно удалить
                        foreach ($dP->categories as $c){
                            $deleteCategory_id = Category::find($c->id);
                            $dP->categories()->detach($deleteCategory_id);
                        }
                        // Удаляем запись о продукте
                        $dP->where('id', '=', $_POST['id'])->delete();
                    }
                } else echo 'ID='.$_POST['id'].' - do not exist!';
                //Обновляем страницу с новыми данными
                $products = Product::all();
                return view('product',['products' => $products]);
        }
    }

}
