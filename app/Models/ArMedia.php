<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArMedia extends Model
{
    protected $table = 'ar_medias';

    protected $fillable = [
        'name', 'type', 'link', 'folder_id', 'status', 'created_at', 'updated_at'
    ];

    //protected $appends = ['languages'];

    public function folderR()
    {
        return $this->belongsTo(ArMediaFolder::class, 'id', 'folder_id');
    }

    public function getLanguagesAttribute()
    {

    }

    public function _getDirMedia($type)
    {
        switch ($type) {
            case TAB_MEDIA_IMAGE:
                return DIR_IMAGE;
                break;
            case TAB_MEDIA_AUDIO:
                return DIR_AUDIO;
                break;
            case TAB_MEDIA_VIDEO:
                return DIR_VIDEO;
                break;
            case TAB_MEDIA_3D:
                return DIR_3D;
                break;
            default:
                return TAB_MEDIA_IMAGE;
                break;
        }
    }

    public function getFileDirAttribute()
    {
        $dir_media_by_type = self::_getDirMedia($this->type);
        $path_media = public_path(DIR_UPLOAD . '/' . DIR_AR . '/' . DIR_MEDIA);

        return $path_media . '/' . $dir_media_by_type . '/' . $this->link;
    }

    public static function contentByLang($spotId, $langId)
    {
        return ObjectContent::query()->where([
            ['object_type', OBJ_TYPE_SPOT], ['language_id', $langId], ['object_id', $spotId]
        ])->get();
    }
}
