<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    protected $table = 'variants';

    protected $fillable = [
        'id',
        'artifact_id',
        'sku',
        'description',
        'price',
        'status',
        'quantity',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}
