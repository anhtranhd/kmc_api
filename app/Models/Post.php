<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Post extends Model implements TranslatableContract
{
    use SoftDeletes;
    use Translatable;
    const ACTIVE = 1; // Hien thi
    const MEDIA_TYPE_IMAGE = 'image';
    const MEDIA_TYPE_AVATAR = 'avatar';
    const TYPE_GENERAL = 'general';
    const TYPE_NEWS = 'news';
    public function getRelationKey() {
        return 'post_id';
    }

    public $timestamps = true;
    protected $table = 'posts';
    public $translatedAttributes = ['title', 'short_title', 'short_desc', 'description'];
    protected $fillable = [
        'id',
        'type',
        'source',
        'status',
        'display_order',
        'tags',
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
        'type' => 'string',
        'source' => 'string',
        'status' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     *
     * Relation
     */

    public function scopeActive($query)
    {
        return $query->where('status', ACTIVE);
    }

    public function medialImageable()
    {
        return $this->morphOne(MedialImageable::class, 'imageable');
    }
}
