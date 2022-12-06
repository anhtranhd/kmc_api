<?php


namespace App\Models;


use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Artifact extends Model implements TranslatableContract
{
    use SoftDeletes;
    use Translatable;

    const IMAGE = 'image';
    const VIDEO = 'video';
    const AUDIO = 'audio';
    const THREE_DIMENSIONAL = '3d';
    public $timestamps = true;
    public $translatedAttributes = ['name', 'origin', 'period', 'content'];
    protected $table = 'artifacts';
    protected $fillable = [
        'id',
        'code',
        'category_id',
        'has3DModel',
        'status',
        'created_by',
        'updated_by',
        'updated_at',
        'created_at',
        'deleted_at',
        'showroom_id'
    ];

    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function showrooms()
    {
        return $this->belongsTo(Showroom::class, 'showroom_id', 'id');
    }

    public function attributes()
    {
        return $this->hasMany(ArtifactAttribute::class, 'artifact_id');
    }

    public function attributesOfArtifact()
    {
        return $this->belongsToMany(Attribute::class, 'artifact_attribute', 'artifact_id', 'attribute_id')->withTrashed()->withTimestamps()->orderBy('order');
    }

    public function artifactFile()
    {
        return $this->hasMany(ArtifactFile::class, 'artifact_id', 'id');
    }
}
