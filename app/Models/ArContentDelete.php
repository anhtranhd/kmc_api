<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArContentDelete extends Model
{
    protected $table = 'ar_content_deleted';

    protected $fillable = ['content_id', 'latitude', 'longitude', 'start_time', 'end_time', 'user_id', 'created_at', 'updated_at'];

    public function contentR()
    {
        return $this->belongsTo(ArContent::class, 'id', 'content_id');
    }

    public function getLanguagesAttribute()
    {
    }

    public static function getListByLocationAndTime($lat, $long, $time)
    {
        $radius = RADIUS_REQUEST;
        $query = self::query();
        $query->whereNotNull('latitude');
        $query->whereNotNull('longitude');

        $distance = "(6371 * acos(cos(radians(latitude))
                     * cos(radians(" . $lat . "))
                     * cos(radians(" . $long . ")
                     - radians(longitude))
                     + sin(radians(latitude))
                     * sin(radians(" . $lat . "))))";
        $query->whereRaw($distance . " <= " . $radius); // km

        if ($time) {
            $query->where('start_time', '<=', $time);
            $query->where('end_time', '>=', $time);
        }

        return $query->pluck('content_id');
    }
}
