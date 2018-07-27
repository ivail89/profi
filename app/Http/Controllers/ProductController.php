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

    // Отобрадение всех продуктов для WEB версии
    public function show(){
        $products = Product::all();
        return view('product',['products' => $products]);
    }
    
    
    // Отображение всех продуктоа по API
    public function apiShow(Request $request){
        $products = Product::all();
        $type = $request->get('type');

        // Массив с данными на выход
        $json_data = array ();
        
        switch ($type){
            case 'show':
                /*
                 * Для вывода продуктов конкретной категории, необходимо указать 
                 * category = ID Категории
                 */
                $category_id = $request->get('category');
                if ($category_id){
                    $categories = Category::with('products')
                        ->where('id', $category_id)
                        ->get();
                    foreach ($categories as $category){
                        foreach ($category->products as $product){
                            array_push($json_data,[
                                    'id' => $product->id,
                                    'name' => $product->name
                                ]);
                        }
                    }
                    break;
                }
                foreach ($products as $product){
                    // Массив категорий одного продукта
                    $arrCategories = array ();

                    // Собираем категории для одного продукта
                    foreach ($product->categories as $p){
                        array_push($arrCategories, $p->name);
                    }
                    array_push($json_data,[
                            'id' => $product->id,
                            'name' => $product->name,
                            'categories' => $arrCategories
                        ]);
                }
                break;
            case 'add':
                /*
                 * Для добавления необходимо указать 
                 * name = Имя нового продукта 
                 * category_id = ID категории товара.
                 */

                $name = $request->get('name');
                $category_id = $request->get('category_id');
                
                //Данная Категория уже существует?
                $addCategory = Category::with('products')
                        ->where('id', $category_id)
                        ->get();
                if (!$addCategory->count()){
                    $res = 'category_id '.$category_id.' - do not exists!';
                    array_push($json_data, $res);
                    break;
                }

                //Данный Продукт уже существует? 
                $addProduct = Product::with('categories')
                    ->where('name', $name)
                    ->get();
                if ($addProduct->count()){
                    $res = 'Product '.$name.' - exists!';
                    array_push($json_data, $res);
                    break;
                }
                
                // Вставка записей в БД
                $addProduct = new Product(['name' => $name]);
                foreach ($addCategory as $aC){
                    $aC->products()->save($addProduct);
                }
                array_push($json_data, 'Product added');
                break;
            case 'delete':
                /*
                 * Для удаления достаточно указать ID удаляемого Продукта
                 * Выполняем проверку - существует ли Продукт с таким ID
                 * Все связи с данным продуктом удаляем из таблицы products_categories
                 */
                $id = $request->get('id');
                $deleteProduct = Product::with('categories')
                    ->where('id', $id)
                    ->get();
                if ($deleteProduct->count()){
                    foreach ($deleteProduct as $dP){
                        // получаем экземпляры Категорий, связь с которыми нужно удалить
                        foreach ($dP->categories as $c){
                            $deleteCategory_id = Category::find($c->id);
                            $dP->categories()->detach($deleteCategory_id);
                        }
                        // Удаляем запись о продукте
                        $dP->where('id', '=', $id)->delete();
                    }
                } else {
                    $res = 'ID='.$id.' - do not exist!';
                    array_push($json_data, $res);
                    break;
                }
               
                array_push($json_data, 'Product deleted');
                break;
            case 'edit':
                /*
                 * Для редактрирования необходимо указать
                 * id - ID редактируемого продукта
                 * category_id - ID редактируемой Категории 
                 * name - новое имя Продукта
                 * Порядок обработки запроса:
                 * 1. Продукт с таким ID существует? Нет - ошибка
                 * 2. Проукт существует, отличается имя обновляем имя Продукта
                 * 3. Имя совпадает, ID Категории добавляем к имеющейся Категории 
                 */
                
                $id = $request->get('id');
                $name = $request->get('name');
                $category_id = $request->get('category_id');

                //Данная Категория уже существует?
                $editCategory = Category::with('products')
                        ->where('id', $category_id)
                        ->get();
                if (!$editCategory->count()){
                    $res = 'category_id '.$category_id.' - do not exists!';
                    array_push($json_data, $res);
                    break;
                }
                
                //ID данного продукта существует?
                $editID = Product::with('categories')
                    ->where('id', $_POST['id'])
                    ->get();
                if (!$editID->count()){
                    $res = 'ID='.$id.' - do not exist!';
                    array_push($json_data, $res);
                    break;
                }
                
                //Данный Продукт (Имя) уже существует? 
                $editProduct = Product::with('categories')
                    ->where('name', $name)
                    ->get();
                if ($editProduct->count()){
                    foreach ($editProduct as $aP){
                        $aP -> categories() -> attach($category_id);
                    }
                    array_push($json_data, 'Category add to Product');
                } else {
                    Product::where('id', '=', $id)
                        ->update(['name' => $name]);
                    array_push($json_data, 'Product edited');
                }
                break;
        };
        
        return response()->json($json_data,201);
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
