<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ArtifactTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'origin', 'period', 'content'];
    protected $table = 'artifact_translations';
}
