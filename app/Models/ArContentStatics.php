<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArContentStatics extends Model
{
    protected $table = 'ar_content_statics';

    protected $fillable = [
        'id',
        'content_id',
        'ime',
        'time',
        'created_at',
        'updated_at'
    ];

    public function arContent()
    {
        return $this->hasOne(ArContent::class, 'id', 'content_id');
    }
}
