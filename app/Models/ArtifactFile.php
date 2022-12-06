<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArtifactFile extends Model
{
    use SoftDeletes;

    const IMAGE = 'image';
    const VIDEO = 'video';
    const AUDIO = 'audio';
    const THREE_DIMENSIONAL = '3d';
    public $timestamps = true;
    protected $table = 'artifact_files';
    protected $fillable = [
        'id',
        'artifact_id',
        'file_id',
        'created_by',
        'updated_by',
        'updated_at',
        'created_at',
    ];

    public function artifacts()
    {
        return $this->belongsTo(Artifact::class, 'artifact_id', 'id');
    }

    public function files()
    {
        return $this->belongsTo(ArMedia::class, 'file_id', 'id');
    }

    public function medialImageable()
    {
        return $this->morphOne(MedialImageable::class, 'imageable');
    }
}
