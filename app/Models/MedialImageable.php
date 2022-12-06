<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedialImageable extends Model
{
    public $timestamps = true;

    protected $table = 'media_imageables';

    protected $fillable = [
        'id',
        'file_id',
        'imageable_id',
        'imageable_type',
        'zone',
        'order',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function imageable() {
        return $this->morphTo();
    }

    public function file() {
        return $this->belongsTo(MediaFile::class, 'file_id');
    }
}
