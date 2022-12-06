<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
    const MP4 = 'video/mp4';
    const IMAGE = [
        'image/png',
        'image/jpeg'
    ];

    public $timestamps = true;

    protected $table = 'media_files';

    protected $fillable = [
        'id',
        'is_folder',
        'filename',
        'path',
        'extension',
        'mimetype',
        'filesize',
        'folder_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}
