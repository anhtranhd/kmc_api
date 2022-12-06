<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArContentMedia extends Model
{
    protected $table = 'ar_content_medias';

    protected $fillable = [
        'content_id',
        'media_id',
        'coordinate_x',
        'coordinate_y',
        'created_at',
        'updated_at'
    ];

    //protected $appends = ['languages'];

    public function mediaR()
    {
        return $this->belongsTo(ArMedia::class, 'id', 'media_id');
    }

    public function contentR()
    {
        return $this->belongsTo(ArContent::class, 'id', 'content_id');
    }

    public static function getItemByContentAndMedia($mediaId, $contentId)
    {
        return self::query()->where([
            ['content_id', $contentId], ['media_id', $mediaId]
        ])->first();
    }
}
