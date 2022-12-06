<?php

namespace App\Models;

use App\Jobs\ARTargetUpdateJob;
use App\Traits\SoftDeleteTrait;
use Illuminate\Database\Eloquent\Model;

class ArContent extends Model
{
    use SoftDeleteTrait;

    const TYPE_CONTENT = 'content';
    const TYPE_BRAND = 'brand';
    const ROUTE_CONTENT = 'contents';
    const ROUTE_BRAND = 'brands';

    protected $table = 'ar_contents';

    protected $fillable = [
        'id',
        'name',
        'language_id',
        'linkable_type',
        'linkable_id',
        'description',
        'type',
        'target_id',
        'status',
        'created_at',
        'updated_at',
        'serialized_data',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($data) {
            dispatch(new ARTargetUpdateJob((int)$data->arTarget->id,
                ['status' => $data->status, 'type' => [
                    "app_id" => config('easy_ar.app_id'),
                    "target_id" => (int)$data->arTarget->id,
                    "file_name" => $data->arTarget->name,
                    "status" => $data->status
                ]]));
        });

        static::updated(function ($data) {
            if ($data->arTarget) {
                dispatch(new ARTargetUpdateJob((int)$data->arTarget->id,
                ['status' => $data->status, 'type' => [
                    "app_id" => config('easy_ar.app_id'),
                    "target_id" => (int)$data->arTarget->id,
                    "file_name" => $data->arTarget->name,
                    "status" => $data->status
                ]]));
                if ($data->target_id != $data->getOriginal('target_id')) {
                    dispatch(new ARTargetUpdateJob((int)$data->getOriginal('target_id'),
                        ['status' => 0, 'type' => [
                            "app_id" => config('easy_ar.app_id'),
                            "target_id" => (int)$data->arTarget->id,
                            "file_name" => $data->arTarget->name,
                            "status" => 0
                        ]]));
                }
            }
        });

        static::deleting(function ($data) {
            dispatch(new ARTargetUpdateJob((int)$data->arTarget->id, ['status' => 0, 'type' => null]));
        });
    }

    /**
     * ar target relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function arTarget()
    {
        return $this->belongsTo(ArTarget::class, 'target_id', 'id');
    }

    /**
     * owner content relation by ar content
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contentR()
    {
        return $this->hasMany(ObjectContent::class, 'object_id', 'id')->where([
            ['object_type', OBJ_TYPE_AR_CONTENT]
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contentCommonR()
    {
        return $this->belongsTo(ObjectContent::class, 'id', 'object_id')->where([
            ['object_type', OBJ_TYPE_AR_CONTENT],
            ['object_name', OBJ_NAME_SERIALIZED_DATA]
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contentStaticsR()
    {
        return $this->belongsTo(ObjectStatics::class, 'id', 'object_id')->where([
            ['object_type', OBJ_TYPE_AR_CONTENT]
        ]);
    }

    /**
     * @param $language_id
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contentByLangR($language_id)
    {
        $data = $this->belongsTo(ObjectContent::class, 'id', 'object_id')->where([
            ['object_type', OBJ_TYPE_AR_CONTENT],
            ['object_name', OBJ_NAME_SERIALIZED_DATA],
            ['language_id', $language_id]
        ]);
        return $data;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function arContentStatics()
    {
        return $this->belongsTo(ArContentStatics::class, 'id', 'content_id');
    }
}
