<?php


namespace App\Models;


use App\Traits\RecordSignature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use RecordSignature;
    use SoftDeletes;

    const AVATAR = 'avatar';
    const BACKGROUND = 'background';
    const FILES = 'files';
    const IMAGE = 'image';
    const VIDEO = 'video';

    const REMOVE_AVATAR = 'removeAvatar';
    const REMOVE_BACKGROUND = 'removeBackground';
    const REMOVE_IMAGES = 'removeImages';
    const REMOVE_VIDEO = 'removeVideo';

    const REMOVE_MEDIA = 2;

    const MAIN = 'main';
    const NOMARL = 'nomarl';

    const MAX_PHONE = 12;
    const MIN_PHONE = 10;

    const MAX_VIDEO_SIZE = 20480;
    const MAX_IAMGE_SIZE = 2048;

    const MIME_TYPES = ['video/mp4', 'image/png', 'image/jpeg', 'image/svg'];

    public $timestamps = true;

    protected $table = 'stores';

    protected $fillable = [
        'id',
        'name',
        'type',
        'province',
        'city',
        'address',
        'description',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Relationships
     */

    public function artifacts()
    {
        return $this->hasMany(Artifact::class, 'store_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'store_id')->withTrashed();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'stores_categories', 'store_id', 'category_id')->withTimestamps();
    }

    public function mainCategory()
    {
        return $this->belongsToMany(Category::class, 'stores_categories', 'store_id', 'category_id')->wherePivot('type', 'like', Store::MAIN);
    }

    public function hierarchyCategories()
    {
        return $this->hasMany(StoreCategory::class, 'store_id')->whereNotNull('type');
    }

    public function medialImageable()
    {
        return $this->morphMany(MedialImageable::class, 'imageable');
    }

    public function mediaImages()
    {
        return $this->morphMany(MedialImageable::class, 'imageable')->where('zone', Store::IMAGE);
    }

    /**
     * Scope
     */
    public function scopeSearchName($query, $name)
    {
        return $query->where('stores.name', 'ilike', "%{$name}%");
    }

    public function scopeUserJoin($query)
    {
        return $query->leftJoin('users', 'users.store_id', '=', 'stores.id');
    }

}
