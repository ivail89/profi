<?php 
use Illuminate\Support\Facades\Schema; 
use Illuminate\Database\Schema\Blueprint; 
use Illuminate\Database\Migrations\Migration; 
class CreateUsersTable extends Migration 
{ 
    /** * Run the migrations. * * @return void */ 
    public function up() { 
        Schema::dropIfExists('users'); 
        Schema::create('users', function (Blueprint $table) { 
            $table->increments('id'); 
            $table->string('name', 100); 
            $table->string('email', 100)->unique()->default('admin@email.ru');
            $table->string('password', 100); 
            $table->rememberToken(); 
            $table->timestamps(); }); 
            
        } 
    /** * Reverse the migrations. * * @return void */ 
    public function down() { 
            Schema::dropIfExists('users'); 
    } 
}