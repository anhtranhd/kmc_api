<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait RecordSignature
{
    /**
     * The name of the "created by" column.
     *
     * @var string
     */
    public static $created_by = 'created_by';

    /**
     * The name of the "updated by" column.
     *
     * @var string
     */
    public static $updated_by = 'updated_by';

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            if (self::hasUpdatedByAttribute($model) && Auth::check()) {
                $model->{self::$updated_by} = Auth::user()->id;
            }
        });
        static::saving(function ($model) {
            if (self::hasCreatedByAttribute($model) && Auth::check()) {
                $model->{self::$created_by} = $model->{self::$created_by} ?? Auth::user()->id;
            }

            if (self::hasUpdatedByAttribute($model) && Auth::check()) {
                $model->{self::$updated_by} = Auth::user()->id;
            }
        });

        static::creating(function ($model) {
            if (self::hasCreatedByAttribute($model) && Auth::check()) {
                $model->{self::$created_by} = $model->{self::$created_by} ?? Auth::user()->id;
            }

            if (self::hasUpdatedByAttribute($model) && Auth::check()) {
                $model->{self::$updated_by} = Auth::user()->id;
            }
        });
    }

    /**
     * Check model has updated by attribute
     * @param $model
     * @return bool
     */
    private static function hasUpdatedByAttribute($model)
    {
        return in_array(self::$updated_by, Schema::getColumnListing($model->getTable()));
    }

    /**
     * Check model has created by attribute
     * @param $model
     * @return bool
     */
    private static function hasCreatedByAttribute($model)
    {
        return in_array(self::$created_by, Schema::getColumnListing($model->getTable()));
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
