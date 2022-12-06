<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObjectStatics extends Model
{
    protected $table = 'object_statics';

    protected $fillable = ['id', 'object_type', 'object_id', 'count', 'created_at', 'updated_at'];

    public function contentR()
    {
        return $this->belongsTo(ArContent::class, 'object_id', 'id')->where('object_type', OBJ_TYPE_AR_CONTENT);
    }

    public function spotR()
    {
        return $this->belongsTo(Spot::class, 'object_id', 'id')->where('object_type', OBJ_TYPE_SPOT);
    }
}
