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
    
    public function show(){
        $categories = DB::select ("SELECT * FROM `categories`");
        return view('categories',['categories' => $categories]);
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
