<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class CategoryTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'description'];
    protected $table = 'category_translations';
}
