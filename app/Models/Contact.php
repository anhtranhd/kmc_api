<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Contact extends Model implements TranslatableContract
{
    use Translatable;

    public $timestamps = true;
    protected $table = 'contacts';
    public $translatedAttributes = ['address'];
    protected $fillable = [
        'id',
        'email',
        'phone_number',
        'fax',
        'website',
        'latitude',
        'longitude',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'email' => 'string',
        'website' => 'string',
        'latitude' => 'string',
        'longitude' => 'string',
        'status' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];
}
