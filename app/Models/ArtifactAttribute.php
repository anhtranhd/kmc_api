<?php


namespace App\Models;


use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class ArtifactAttribute extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = 'artifact_attribute';
    public $timestamps = true;
    public $translatedAttributes = ['attribute_value'];

    protected $fillable = [
        'id',
        'artifact_id',
        'attribute_id',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
}
