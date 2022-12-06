<?php

namespace App\Services;

use App\Models\Artifact;
use App\Models\ArtifactFile;
use App\Models\Category;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Lang;
use stdClass;
use URL;


/**
 * Class ArtifactService
 *
 * @package App\Services
 */
class ArtifactService extends BaseService
{

    /**
     * Get artifact builder.
     *
     * @return artifact|Builder
     */
    public function getArtifactBuilder()
    {

        $artifacts = Artifact::with('categories', 'showrooms', 'attributes')->where('status', STATUS_ACTIVE);
        return $artifacts;
    }

    /**
     * Get artifact builder.
     *
     * @return artifact|Builder
     */
    public function checkArtifact($id)
    {

        $artifact = Artifact::where('id', $id)->where('status', STATUS_ACTIVE)->first();

        return $artifact;
    }

    public function transformArtifact($artifact)
    {
        $attributesOfArtifact = $artifact->attributesOfArtifact()->get()->toArray();
        $attributesValue = $artifact->attributes()->get();
        $attributes = [];

        foreach ($attributesOfArtifact as &$attribute) {
            foreach ($attributesValue as $item) {
                if ($attribute['id'] == $item->attribute_id && $item->status == STATUS_ACTIVE) {
                    $obj = [];
                    $obj['id'] = $attribute['id'];
                    $obj['type'] = $attribute['type'];
                    $obj['is_required'] = @$attribute['is_required'];
                    $obj['is_translate'] = @$attribute['is_translate'];
                    $obj['status'] = $item->status;
                    $obj['title'] = $attribute['title'];
                    $obj['value'] = @$attribute['is_translate'] ? $item['attribute_value'] : $item->translate('vi')->attribute_value;
                    $attributes[] = (object)$obj;
                    break;
                }
            }
        }
        $images = [];
        $videos = [];
        $audios = [];
        $arModel = [];

        foreach ($artifact->artifactFile as $file) {
            $fileObj = new stdClass();
            $fileObj->id = $file->id;

            switch ($file->files->type) {
                case ArtifactFile::IMAGE:
                    $fileObj->path = $this->storageService->url(DIR_AR . '/' . DIR_MEDIA . '/' . $this->_getDirMedia($file->files->type) . '/' . $file->files->link);
                    array_push($images, $fileObj);
                    break;
                case ArtifactFile::VIDEO:
                    $fileObj->path = $this->storageService->url(DIR_AR . '/' . DIR_MEDIA . '/' . $this->_getDirMedia($file->files->type) . '/' . $file->files->link);
                    $fileObj->duration = @$file->files->duration ?: '';
                    array_push($videos, $fileObj);
                    break;
                case ArtifactFile::AUDIO:
                    $fileObj->path = $this->storageService->url(DIR_AR . '/' . DIR_MEDIA . '/' . $this->_getDirMedia($file->files->type) . '/' . $file->files->link);
                    $fileObj->duration = @$file->files->duration ?: '';
                    array_push($audios, $fileObj);
                    break;
                case ArtifactFile::THREE_DIMENSIONAL:
                    $fileObj->path = $this->storageService->url(DIR_AR . '/' . DIR_MEDIA . '/' . $this->_getDirMedia($file->files->type) . '/' . $file->files->link);
                    array_push($arModel, $fileObj);
                    break;
                default:
                    break;
            }
        }

        $avatar = null;
        $media = $this->medialImageableService->findByManyWithObject(null, Artifact::class, ['*'], $artifact->id, false);
        if ($media) {
            if ($media->zone == 'avatar') {
                $avatar = $this->storageService->url($media->file->path);
            }
        }
        $data = [
            'id' => $artifact->id,
            'name' => $artifact->name,
            'code' => $artifact->code,
            'status' => @$artifact->status,
            'attributes' => $attributes,
            'showroom' => $artifact->showrooms ? $this->transformerShowroom($artifact->showrooms) : $artifact->showrooms,
            'category' => $artifact->categories ? $this->transformerCategory($artifact->categories) : $artifact->categories,
            'content' => $artifact->content,
            'origin' => $artifact->origin ? Lang::get('nations.' . $artifact->origin) : '',
            'avatar' => $avatar,
            'images' => @$images,
            'videos' => @$videos,
            'audios' => @$audios,
            'arModel' => @$arModel,
        ];

        return (object)$data;
    }

    public function _getDirMedia($type)
    {
        $dirMedia = [
            TAB_MEDIA_IMAGE => DIR_IMAGE,
            TAB_MEDIA_AUDIO => DIR_AUDIO,
            TAB_MEDIA_VIDEO => DIR_VIDEO,
            TAB_MEDIA_3D => DIR_3D
        ];

        return $dirMedia[$type] ?? DIR_IMAGE;
    }

    public function getTotalArtifactByShowroomId($showroomId)
    {

        $total = Artifact::where('showroom_id', $showroomId)->where('status', STATUS_ACTIVE)->count();

        return $total;
    }

    public function getTotalArtifactByCategoryId($categoryId)
    {
        $hierarchy = "/{$categoryId}/";
        $idCategories = Category::query()->where('hierarchy', 'like', $hierarchy . '%')->get()->pluck('id')->toArray();
        $total = Artifact::whereIn('category_id', $idCategories)->where('status', STATUS_ACTIVE)->count();

        return $total;
    }
}
