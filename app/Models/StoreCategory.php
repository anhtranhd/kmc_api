<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class StoreCategory extends Model
{
    protected $table = 'stores_categories';

    protected $fillable = [
        'store_id',
        'category_id',
        'created_by',
        'updated_by',
        'updated_at',
        'created_at',
        'type'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
