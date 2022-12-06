<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageService
{

    private $disk;

    public function __construct()
    {
        $this->disk = config('filesystems.disk');
    }

    public function put($path, $content, $opts = [])
    {
        return Storage::disk($this->disk)->put($path, $content, $opts);
    }

    public function delete($path)
    {
        return Storage::disk($this->disk)->delete($path);
    }

    public function url($path)
    {
        $token = md5(Str::random());
//        $this->setRedis($token, $path, ['expire' => 'EX', 'time' => 120]);
        return Storage::disk($this->disk)->url($path); // . '?token=' . $token
    }
}
