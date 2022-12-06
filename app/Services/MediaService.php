<?php


namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Models\MediaFile;
use App\Models\MedialImageable;
use App\Services\StorageService;

class MediaService
{
    public function storeMediaFile(UploadedFile $file, Model $model, $storagePath = null, $zone = null)
    {

        $oriName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        $storageService = new StorageService();
        $fileName = $fileName ?? pathinfo($oriName, PATHINFO_FILENAME) . '_' . time() . '.' . $extension;
        $storagePath = $storagePath ?? $fileName;
        $storageService->put($storagePath, file_get_contents($file));
        
        $mediaFile = new MediaFile();
        $mediaFile->is_folder = false;
        $mediaFile->filename = $fileName;
        $mediaFile->path = $storagePath;
        $mediaFile->extension = $extension;
        $mediaFile->mimetype = $file->getMimeType();
        $mediaFile->filesize = $file->getSize();
        $mediaFile->save();

        $mediaImageable = new MedialImageable();

        $mediaImageable->file_id = $mediaFile->id;
        $mediaImageable->imageable_id = $model->id;
        $mediaImageable->imageable_type = get_class($model);
        $mediaImageable->zone = $zone ?? 'media_single';
        $mediaImageable->save();
    }

    public function deleteByIds($ids)
    {
        $query = MediaFile::query();
        return $query->whereIn('id', $ids)->delete();
    }

    public function uploadFile($file, $data)
    {
        $oriName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        $fileName = $fileName ?? pathinfo($oriName, PATHINFO_FILENAME) . '_' . time() . '.' . $extension;
        $storagePath = $storagePath ?? '/' . $data['obj'] . '/' . $fileName;
        Storage::disk('public')->put($storagePath, file_get_contents($file));

        $dataFile = [
            'is_folder' => $data['is_folder'] ?? false,
            'filename' => $fileName,
            'path' => $storagePath,
            'extension' => $extension,
            'mimetype' => $file->getMimeType(),
            'filesize' => $file->getSize(),
            'folder_id' => $data['folder_id'] ?? null
        ];

        return $this->create($dataFile);
    }

    public function destroy($model)
    {
        Storage::disk('public')->delete($model->path);
        return parent::destroy($model);
    }
}
