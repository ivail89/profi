<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    //
    protected $fillable = ['name'];
    
    public function categories(){
        return $this->belongsToMany('App\Category', 'products_categories');
    }
}

