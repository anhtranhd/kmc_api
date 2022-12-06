<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShowroomTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'description'];
    protected $table = 'showroom_translations';
}
