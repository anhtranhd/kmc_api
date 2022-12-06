<?php

namespace App\Models;

use App\Traits\RecordSignature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Category extends Model implements TranslatableContract
{
    use SoftDeletes, RecordSignature;
    use Translatable;

    const ACTIVE = 1;
    const PARENT_ID = 0;
    public function getRelationKey() {
        return 'category_id';
    }
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'parent_id' => 'nullable|integer',
        'name' => 'required|string|max:255|check_unique:categories,name',
        'description' => 'nullable|string|max:300',
        'order' => 'nullable|integer',
        'file' => 'sometimes|mimes:jpg,jpeg,png|max:2048'
    ];

    public $table = 'categories';
    public $translatedAttributes = ['name', 'description'];
    public $fillable = [
        'parent_id',
        'hierarchy',
        'order',
        'status',
        'created_by',
        'updated_by',
        'deleted_at',
        'created_at',
        'updated_at',
    ];


    /**
     * Scope
     *
     */

    public function scopeActive($query)
    {
        return $query->where('status', self::ACTIVE);
    }

    /**
     * Relationship
     *
     */

    public function subcategory()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, CategoryAttribute::class)->withPivot('is_required')->orderBy('order');
    }

    public function artifacts()
    {
        return $this->hasMany(Artifact::class, 'category_id');
    }

    public function medialImageable()
    {
        return $this->morphOne(MedialImageable::class, 'imageable');
    }
    public function parent()
    {
        return $this->hasOne(Category::class, 'parent_id','id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'id','parent_id');
    }
}