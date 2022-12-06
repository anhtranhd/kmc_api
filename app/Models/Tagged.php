<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagged extends Model
{
    public $timestamps = true;

    protected $table = 'tag_tagged';

    protected $fillable = [
        'tag_id', 'taggable_id', 'taggable_type', 'created_at', 'update_at'
    ];

    public function taggable()
    {
        return $this->morphTo();
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }

}
