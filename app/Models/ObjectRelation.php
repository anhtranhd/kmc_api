<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObjectRelation extends Model
{
    protected $table = 'object_relation';

    protected $fillable = ['object_type', 'object_id', 'object_typeR', 'object_idR', 'created_at', 'updated_at'];

    public function spotR()
    {
        return $this->belongsTo(Spot::class, 'object_idR', 'id');
    }
}
