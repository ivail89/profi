Тестовое задание для компании PROFI.RU на должность PHP- разработчик
Выполнил Иван Миронов ivail89@mail.ru 

Разработа выполнялась: 
фреймворr Laravel 
WAMP
IDE NetBeans
postman

Реализована API для каталога товаров
Все запросы POST

Авторизация OAuth2
http://localhost/profi/laravel/public/oauth/token
Дополнительные параметры
grant_type = password
client_id = 1
client_secret = scZBvnQJ1Bh0uEqKNqKv0Jaxmasl4jVIYXT3yGkP
username = a@a.ru	
password = 123123
scope = *

Категории товаров:
http://localhost/profi/laravel/public/api/categories
- Просмотр всех категорий
--ополнительный параметр type = show
--Ответ в формате JSON ID и имя (name) Категории

- Доавить категорию
-- Дополнительные параметры:
--- type = add
--- name Имя новой Категории.
--- Перед добавлением выполняем проверку, есть ли Категория с таким именем.
-- Ответы в формате JSON
--- 'Category '.$name.' - exists!'
--- Category added

- Удалить категорию
-- Дополнительные параметры:
--- type = delete
--- id Категории
--- Выполняем проверку - существует ли Категория с таким ID
-- Ответы в формате JSON
--- 'ID='.$id.' - do not exist!'
--- Category deleted

- Редактировать категорию
-- Дополнительные параметры:
--- type = edit
--- ID редактируемой категории
--- name - новое имя
-- Две проверки:
--- 1. Есть ли Категория с таким именем
--- 2. Новое имя ранее не встречалось
-- Ответы в формате JSON
--- 'Category '.$name.' - exist with other ID'
--- 'ID='.$id.' - do not exist!'
--- Category edited

Товары:
http://localhost/profi/laravel/public/api/products
- Просмотр всех товаров
-- Параметр type = show
-- Ответ в формате JSON ID, имя товара, список Категорий
- Просмотр товаров опеределённой Категории
-- Параметры 
--- type = show
--- category id категории
-- Ответ в формате JSON ID, имя товара, список Категорий

- Доавить продукт
-- Дополнительные параметры:
--- type = add
--- category_id = ID категории товара
--- name Имя новой Категории.
-- Ответы в формате JSON
--- category_id '.$category_id.' - do not exists!'
--- 'Product '.$name.' - exists!'
--- 'Product added'

- Удалить товар
-- Дополнительные параметры:
--- type = delete
--- id Товара
--- Все связи с данным продуктом удаляем из таблицы products_categories
-- Ответы в формате JSON
--- 'ID='.$id.' - do not exist!'
--- Product deleted

- Редактировать товар
-- Дополнительные параметры:
--- type = edit
--- id - ID редактируемого продукта
--- category_id - ID редактируемой Категории 
--- name - новое имя Продукта
-- Порядок обработки запроса:
--- 1. Продукт с таким ID существует? Нет - ошибка
--- 2. Проукт существует, отличается имя обновляем имя Продукта
--- 3. Имя совпадает, ID Категории добавляем к имеющейся Категории
-- Ответы в формате JSON
--- 'category_id '.$category_id.' - do not exists!'
--- Category add to Product
--- Product edited
