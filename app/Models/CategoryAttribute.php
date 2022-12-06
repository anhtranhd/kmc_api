<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoryAttribute extends Pivot
{

    public $table = 'category_attributes';
    public $fillable = [
        'category_id',
        'attribute_id',
        'is_required',
        'order',
        'created_by',
        'updated_by'
    ];

}
