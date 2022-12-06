<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ButtonTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['dom_html'];
    protected $table = 'button_translations';
}
