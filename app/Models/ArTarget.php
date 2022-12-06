<?php

namespace App\Models;

use App\Jobs\ARTargetCreateJob;
use App\Jobs\ARTargetDeleteJob;
use Illuminate\Database\Eloquent\Model;

class ArTarget extends Model
{
    protected $table = 'ar_targets';

    protected $fillable = [
        'id',
        'name',
        'image',
        'rating',
        'easy_ar_id',
        'created_at',
        'updated_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($data) {
            dispatch(new ARTargetCreateJob((int)$data->id));
        });

        static::deleting(function ($data) {
            dispatch(new ARTargetDeleteJob((string)$data->easy_ar_id));
        });
    }

    public function arContent()
    {
        return $this->hasOne(ArContent::class, 'target_id', 'id');
    }

    public function arIndoor()
    {
        return $this->belongsToMany(ArIndoor::class, 'ar_indoor_target', 'ar_target_id',
            'ar_indoor_id')->withPivot('latitude', 'longitude');
    }

    public function arContentR()
    {
        return $this->hasMany(ArContent::class, 'target_id', 'id');
    }
}
