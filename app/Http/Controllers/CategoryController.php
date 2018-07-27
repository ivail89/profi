<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Category;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    // Отобрадение всех Категорий для WEB версии
    public function show(){
        $categories = DB::select ("SELECT * FROM `categories`");
        return view('categories',['categories' => $categories]);
    }

    // Отображение всех продуктоа по API
    public function apiShow(Request $request){
        $categories = Category::all();
        $type = $request->get('type');

        // Массив с данными на выход
        $json_data = array ();
        
        switch ($type){
            case 'show':
                foreach ($categories as $category){
                    array_push($json_data,[
                            'id' => $category->id,
                            'name' => $category->name
                        ]);
                }
                break;
            case 'add':
                /*
                 * Для добавления достаточно указать name Имя новой Категории.
                 * Перед добавлением выполняем проверку, есть ли Категория с таким именем.
                 */
                $name = $request->get('name');
                $add = Category::where('name', '=', $name)->get();
                if (!$add->count()){
                    Category::insert(
                        ['name' => $name]
                    );
                } else {
                    $res = 'Category '.$name.' - exists!';
                    array_push($json_data, $res);
                    break;
                }
                //Обновляем страницу с новыми данными
                array_push($json_data, 'Category added');
                break;
            case 'delete':
                /*
                 * Для удаления достаточно указать ID удаляемой Категории
                 * Выполняем проверку - существует ли Категория с таким ID
                 */
                $id = $request->get('id');
                $delete = Category::where('id', '=', $id)->get();
                // Проверям на существование записи в БД
                if ($delete->count()){
                    Category::where('id', '=', $id)
                            ->delete();
                } else {
                    $res = 'ID='.$id.' - do not exist!';
                    array_push($json_data, $res);
                    break;
                }

                array_push($json_data, 'Category deleted');
                break;
            case 'edit':
                /*
                 * Для редактрирования необходимо указать 
                 * ID редактируемой категории 
                 * name - новое имя.
                 * Две проверки:
                 * 1. Есть ли Категория с таким именем
                 * 2. Новое имя ранее не встречалось
                 */
                $id = $request->get('id');
                $name = $request->get('name');
                $edit = Category::where('id', '=', $id)->get();
                if ($edit->count()){
                    $edit = Category::where('name', '=', $name)->get();
                    if (!$edit->count()){
                        Category::where('id', '=', $id)
                            ->update(['name' => $name]);
                    } else {
                        $res = 'Category '.$name.' - exist with other ID';
                        array_push($json_data, $res);
                        break;
                    }
                } else {
                    $res = 'ID='.$id.' - do not exist!';
                    array_push($json_data, $res);
                    break;
                }
                array_push($json_data, 'Category edited');
                break;
        };
        
        return response()->json($json_data,201);
    }
    
    // Обновление данных на основании запроса из формы редактирования
    public function update(){
        switch ($_POST['type']){
            case 'ADD':
                /*
                 * Для добавления достаточно указать Имя новой Категории.
                 * Перед добавлением выполняем проверку, есть ли Категория с таким именем.
                 */
                $add = Category::where('name', '=', $_POST['category'])->get();
                if (!$add->count()){
                    Category::insert(
                        ['name' => $_POST['category']]
                    );
                } else echo "This category exist!";
                //Обновляем страницу с новыми данными
                $categories = Category::all();
                return view('categories',['categories' => $categories]);
            case 'EDIT':
                /*
                 * Для редактрирования необходимо указать ID редактируемой категории и новое имя.
                 * Две проверки:
                 * 1. Есть ли Категория с таким именем
                 * 2. Новое имя ранее не встречалось
                 */
                $edit = Category::where('id', '=', $_POST['id'])->get();
                if ($edit->count()){
                    $edit = Category::where('name', '=', $_POST['category'])->get();
                    if (!$edit->count()){
                        Category::where('id', '=', $_POST['id'])
                            ->update(['name' => $_POST['category']]);
                    } else echo $_POST['category']." - exist with other ID";
                } else echo "ID = ".$_POST['id']." - This category don't exist!";
                //Обновляем страницу
                $categories = Category::all();
                return view('categories',['categories' => $categories]);
            case 'DELETE':
                /*
                 * Для удаления достаточно указать ID удаляемой Категории
                 * Выполняем проверку - существует ли Категория с таким ID
                 */
                $delete = Category::where('id', '=', $_POST['id'])->get();
                // Проверям на существование записи в БД
                if ($delete->count()){
                    Category::where('id', '=', $_POST['id'])
                            ->delete();
                } else echo $_POST['id']," - This category don't exist!";
                //Обновляем страницу
                $categories = Category::all();
                return view('categories',['categories' => $categories]);
        }
    }
}
