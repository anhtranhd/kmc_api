<?php

namespace App\Models;
use App\Models\User;
use App\Traits\RecordSignature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Showroom extends Model implements TranslatableContract
{
    use RecordSignature;
    use SoftDeletes;
    use Translatable;
    const ACTIVE = 1; // Hien thi

    public function getRelationKey() {
        return 'showroom_id';
    }

    public $timestamps = true;
    protected $table = 'showrooms';
    public $translatedAttributes = ['name', 'description'];
    protected $fillable = [
        'id',
        'code',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'code' => 'string',
        'status' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string|max:100|unique:showrooms,name',
        'code' => 'required|string|max:100|unique:showrooms,code',
        'description' => 'nullable|string|max:300',
        'status' => 'nullable|integer',
        'created_by' => 'nullable|integer',
        'updated_by' => 'nullable|integer',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
//      Logo, danh muc
        'image' => 'sometimes|mimes:jpg,png,svg,jpeg',
    ];

    /**
     * Scope
     *
     */

    public function scopeSearchByColumn($query, $column, $name)
    {
        $value = strtolower($name);
        return $query->whereRaw("LOWER({$column}) LIKE '%{$value}%'");
    }

    /**
     *
     * Relation
     */

    public function scopeActive($query)
    {
        return $query->where('status', STATUS_ACTIVE);
    }

    public function medialImageable()
    {
        return $this->morphOne(MedialImageable::class, 'imageable');
    }

    public function artifacts()
    {
        return $this->hasMany(artifact::class, 'showroom_id');
    }
}
