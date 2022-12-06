<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArContentSpot extends Model
{
    protected $table = 'ar_content_spot';

    protected $fillable = [
        'content_id',
        'spot_id',
        'coordinate_x',
        'coordinate_y',
        'created_at',
        'updated_at'
    ];


    public function spotR()
    {
        return $this->belongsTo(Spot::class, 'id', 'spot_id');
    }

    public function contentR()
    {
        return $this->belongsTo(ArContent::class, 'id', 'content_id');
    }

    public function getLanguagesAttribute()
    {
    }

    public static function getItemByContentAndSpot($spotId, $contentId)
    {
        return self::query()->where([
            ['content_id', $contentId], ['spot_id', $spotId]
        ])->first();
    }
}
