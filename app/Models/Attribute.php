<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Attribute extends Model implements TranslatableContract
{
    use SoftDeletes;
    use Translatable;
    public $translatedAttributes = ['title'];

    public static $STATUS_ACTIVE = 1;
    public static $STATUS_INACTIVE = 0;
    
    public static $TYPE_TEXT = 'text';
    public static $TYPE_TEXT_AREA = 'text_area';
    public static $TYPE_DATE_TIME = 'date_time';
    public static $TYPE_NUMBER = 'number';

    public $timestamps = true;
    protected $table = 'attributes';
    protected $fillable = [
        'id',
        'type',
        'is_require',
        'is_translate',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];


    /**
     * attribute option relation
     * @return HasMany
     */
    public function attributeOptions()
    {
        return $this->hasMany(AttributeOption::class, 'attribute_id', 'id');
    }
}
