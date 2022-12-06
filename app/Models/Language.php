<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use SoftDeletes;

    protected $table = 'languages';
    protected $fillable = [
        'id',
        'priority',
        'vi_name',
        'en_name',
        'native_name',
        'google_code',
        'status',
        'is_rtl',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function contentNameR()
    {
        return $this->belongsTo(ObjectContent::class, 'id', 'object_id')->where([
            ['object_type', OBJ_TYPE_LANGUAGE],
            ['object_name', OBJ_NAME_NAME],
            ['language_id', $this->getLangIdByCode(LANG_VIETNAMESE_CODE)],
        ]);
    }

    public static function getLangIdByCode($code)
    {
        return (self::query()->where('google_code', $code)->first())->id ?? 0;
    }
}
