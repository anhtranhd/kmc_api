<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public $timestamps = true;

    protected $table = 'tags';

    protected $fillable = [
        'name', 'slug', 'created_at', 'update_at'
    ];

}
