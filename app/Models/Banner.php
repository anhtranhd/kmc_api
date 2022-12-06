<?php


namespace App\Models;


use App\Traits\RecordSignature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use RecordSignature;
    use SoftDeletes;

    public static $STATUS_ACTIVE = 1;
    public static $STATUS_INACTIVE = 0;
    const WEB = 1;
    const MOBILE = 2;
    const REMOVE_FILE = 2;

    public $timestamps = true;

    protected $table = 'banners';

    protected $fillable = [
        'id',
        'name',
        'description',
        'link',
        'order',
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
        'name' => 'string',
        'link' => 'string',
        'order' => 'integer',
        'status' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];


    /**
     * Scope
     */

    public function scopeActive($query)
    {
        return $query->where('status', STATUS_ACTIVE);
    }

    public function scopeFileMediaJoin($query)
    {
        return $query->leftJoin('media_imageables', function ($sub) {
            $sub->on('media_imageables.imageable_id', '=', 'banner.id')->where('media_imageables.imageable_type', '=', Banner::class);
        })->leftJoin('media_files', function ($subQuery) {
            $subQuery->on('media_files.id', '=', 'media_imageables.file_id');
        });
    }

    /**
     * Relation
     *
     */

    public function medialImageable()
    {
        return $this->morphOne(MedialImageable::class, 'imageable');
    }
}
