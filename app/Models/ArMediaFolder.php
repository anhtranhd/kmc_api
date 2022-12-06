<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArMediaFolder extends Model
{
    protected $table = 'ar_media_folders';

    protected $fillable = [
        'name', 'status', 'type'
    ];

    public $timestamps = true;

    //protected $appends = ['languages'];

    public function getLanguagesAttribute()
    {
    }

    public static function contentByLang($spotId, $langId)
    {
        return ObjectContent::query()->where([
            ['object_type', OBJ_TYPE_SPOT], ['language_id', $langId], ['object_id', $spotId]
        ])->get();
    }
}
