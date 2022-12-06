<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ArtifactAttributeTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['attribute_value'];
    protected $table = 'artifact_attribute_translations';
}
