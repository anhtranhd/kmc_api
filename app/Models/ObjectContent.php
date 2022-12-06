<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObjectContent extends Model
{
    protected $table = 'object_content';
    protected $fillable = [
        'object_type',
        'object_id',
        'language_id',
        'object_name',
        'object_value',
        'created_at',
        'updated_at'
    ];

    /**
     * lang relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function langR()
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }

    /**
     * Find object by type
     * @param $query
     * @param $type
     * @return mixed
     */
    public function scopeObjectByType($query, $type)
    {
        return $query->where('object_content.object_type', $type);
    }
}
