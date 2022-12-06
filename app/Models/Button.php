<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Button extends Model implements TranslatableContract
{
    use Translatable, SoftDeletes;

    public $timestamps = true;
    public $translatedAttributes = ['dom_html'];
    protected $table = 'buttons';
    protected $fillable = [
        'id',
        'dom_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function getRelationKey()
    {
        return 'button_id';
    }
}
