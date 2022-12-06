<?php

namespace App\Http\Controllers;

use App\Models\ArContent;
use App\Models\ArMedia;
use App\Models\ArTarget;
use App\Models\Language;
use App\Models\Spot;
use App\Services\ArContentService;
use App\Services\ArtifactService;
use App\Services\StorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

require_once(public_path('lib/getID3-master/getid3/getid3.php'));

class ArController extends Controller
{

    private $arContentService;
    private $artifactService;

    private $width_scale = 3150;
    /**
     * @var StorageService
     */
    private $storageService;

    public function __construct(
        ArContentService $arContentService,
        ArtifactService $artifactService,
        StorageService $storageService
    )
    {
        parent::__construct();
        $this->artifactService = $artifactService;
        $this->arContentService = $arContentService;
        $this->storageService = $storageService;
    }



    public function detail(Request $request)
    {
        $lang = $request->get('lang') ? $request->get('lang') : 'vi';
        app()->setLocale($lang);
        $validator = $this->arContentService->validateDetailArContentRequest($request);

        if (!$validator->passes()) {
            return $this->_errorResponse($validator->messages()->first());
        }
        $data = [];

        $target = ArTarget::where('easy_ar_id', $request->get('target_id'))->first();

        if ($target) {
            switch ($request->get('type')) {
                case 'indoor':
                case 'content' :
                case 'brand':
                    $data['ar'] = $this->_getContentByTarget($target, $request->get('type'), $lang);
                    break;
                default:
                    break;
            }
            $arContent = ArContent::where('target_id', $target->id)->first();
            if ($arContent->linkable_type && $arContent->linkable_id) {
                if ($arContent->linkable_type == 'artifact') {
                    $artifact = $this->artifactService->checkArtifact($arContent->linkable_id);
                    if ($artifact) {
                        $data['artifact'] = $this->artifactService->transformArtifact($artifact);
                    }
                }
            }
            if (!empty($data['ar'])) {
                return $this->_successResponse($data, 'Get ar content success');
            } else {
                return $this->_errorResponse("Cannot get Target detail");
            }
        }
        return $this->_errorResponse("Cannot get Target detail");
    }

    public function _getContentByTarget($target, $type, $lang = LANG_VIETNAMESE_CODE)
    {
        $arContent = $target->arContent;
        if ($arContent && $arContent->type !== $type) {
            return [];
        }

        $langId = Language::getLangIdByCode($lang);
        $serialized_data = $this->_getDataByLangId($arContent, $langId);
        $dataAr = $serialized_data ? (json_decode($serialized_data))->objects : [];
        $listSpotAndMedia = $this->_getListSpotMedia();
        if (count($dataAr)) {
            $filePath = $this->storageService->url($arContent->arTarget->image);
            $fileSize = $this->getDetailFile($filePath);

            $layers = $this->_getLayers($dataAr, $target, $listSpotAndMedia->medias, $arContent->id,
                $langId);
            $widthScale = round($dataAr[0]->width * $dataAr[0]->scaleX);
            $heightScale = round($dataAr[0]->height * $dataAr[0]->scaleY);
            $data = [
                'id' => $arContent->id,
                'name' => $arContent->name,
                'link' => $dataAr[0]->src,
                'size' => $fileSize,
                'width' => $widthScale,
                'height' => $heightScale,
                'x' => 0,
                'y' => 0,
                'layers' => $layers,
                'created' => strtotime($arContent->created_at),
                'modified' => strtotime($arContent->updated_at),
            ];

        }
        return $data ?? [];
    }

    public function _getDataByLangId($arContent, $langId)
    {
        if (!$arContent) return null;
        $serialized_data = $arContent->serialized_data;

        if ($arContent && $arContent->language_id != AR_LANGUAGE_COMMON) {
            $serialized_data = $arContent->contentByLangR($langId)->first() ? $arContent->contentByLangR($langId)->first()->object_value : null;
        }

        return $serialized_data;
    }

    public function _getListSpotMedia()
    {
        $list = new \stdClass();
        $list->spots = [];
        $list->medias = [];

        $mediaItems = ArMedia::all();
        if (count($mediaItems)) {
            foreach ($mediaItems as $mediaItem) {
                $list->medias[$mediaItem->id] = $mediaItem;
            }
        }

        return $list;
    }

    public function getDetailFile($file_path)
    {
        if (!file_exists($file_path)) {
            return 0;
        }

        $getID3 = new \getID3;
        $file = $getID3->analyze($file_path);
        return $file['filesize'];
    }

    public function _getLayers($dataAr, $modelTarget, $medias, $contentId, $langId)
    {
        $layers = [];
        $i = 0;
        $type_item = '';
        foreach ($dataAr as $index => $item) {
            if (empty($item->entityType)) {
                continue;
            }

            $entityType = trim($item->entityType);
            $entity = $this->__getTypeObject($entityType);

            if ($index == 0) {
                $type_item = $item->entityType;
                $layers[$i] = [
                    'index' => $i,
                    'items' => []
                ];
            } elseif ($index == 1) {
                $dataItem = $this->__getDataItem($index, $item, $entity, $dataAr[0], $modelTarget, $medias, $contentId,
                    $langId);
                $layers[$i]['items'][] = $dataItem;
                $type_item = $item->entityType;
            } else {
                if ($type_item !== $item->entityType || strpos($item->entityType, "media") !== false) {
                    $i++;
                    $type_item = $item->entityType;

                    $dataItem = $this->__getDataItem($index, $item, $entity, $dataAr[0], $modelTarget, $medias, $contentId,
                        $langId);
                    $layers[$i] = [
                        'index' => $i,
                        'items' => [$dataItem]
                    ];
                } else {
                    $dataItem = $this->__getDataItem($index, $item, $entity, $dataAr[0], $modelTarget, $medias, $contentId,
                        $langId);
                    $layers[$i]['items'][] = $dataItem;
                }
            }
        }

        return $layers;
    }

    public function __getTypeObject($entityType)
    {
        $entity = new \stdClass();
        $entity->object_type = $entityType;
        $entity->type_file = '';

        if (substr_count($entityType, '-')) {
            $entityType = explode('-', $entityType);
            $entity->object_type = $entityType[0];
            $entity->type_file = $entityType[1];
        }

        return $entity;
    }

    public function __getDataItem($index, $item, $entity, $target, $modelTarget, $medias, $contentId, $langId)
    {
        $link = !empty($item->entitySrc) ? $item->entitySrc : $item->src;
        $object_type = $entity->object_type;
        $type_file = $entity->type_file;

        if ($object_type === OBJ_TYPE_TARGET) {
            $object_name = $modelTarget->name ?: 'Item ' . $index;
            $created = strtotime($modelTarget->created_at) ?: 0;
            $modified = strtotime($modelTarget->updated_at) ?: 0;
        } else {
            $object_name = !empty($medias[$item->entityId]) ? $medias[$item->entityId]->name : 'Item ' . $index;
            $created = !empty($medias[$item->entityId]) ? strtotime($medias[$item->entityId]->created_at) : 0;
            $modified = !empty($medias[$item->entityId]) ? strtotime($medias[$item->entityId]->updated_at) : 0;
        }

        $fileSize = $this->getDetailFile($link);

        $isGif = substr_count(strtolower($link), 'gif');
        if ($object_type === OBJ_TYPE_AR_MEDIA && $isGif) {
            $type_file = 'gif';
        }

        // get link is image crop
        $width = $item->width;
        $height = $item->height;
        if ($item->clipTo && $object_type === OBJ_TYPE_AR_MEDIA && $type_file === TAB_MEDIA_IMAGE) {
            $contentPath = public_path(DIR_STORAGE . '/' . DIR_AR . '/' . DIR_CONTENT . '/' . $contentId . '/' . DIR_MEDIA_CROP);
            $position = strpos($link, '/storage');
            $mediaPath = public_path(substr($link, $position));
            $mediaPathArr = explode('/', $mediaPath);
            $file_name = !empty($item->fileCrop) ? $item->fileCrop : $mediaPathArr[count($mediaPathArr) - 1];

            if (file_exists($contentPath . '/' . $file_name)) {
                $link = URL::asset(DIR_STORAGE . '/' . DIR_AR . '/' . DIR_CONTENT . '/' . $contentId . '/' . DIR_MEDIA_CROP . '/' . $file_name);
            }

            list($x, $y, $width, $height, $xCoor, $yCoor) = $this->__getParamCrop($item->clipTo);
            $width = round(intval($width));
            $height = round(intval($height));
        }

        $widthData = round($width * $item->scaleX);
        $heightData = round($height * $item->scaleY);

        $coordinates = $this->_getCoordinates($item, $target, $widthData, $heightData);

        return array_merge([
            'id' => $item->entityId,
            "group_layer" => (int)@$item->groupLayer ?? 0,
            'object_type' => $object_type,
            'type' => $type_file,
            'name' => $object_name,
            'width' => $widthData,
            'height' => $heightData,
            'x' => $coordinates->left,
            'y' => $coordinates->top,
            'link' => $link,
            'size' => $fileSize,
            'created' => $created,
            'modified' => $modified,
            "angle" => $item->angle,
            "flipX" => $item->flipX,
            "flipY" => $item->flipY,
        ]);
    }

    public function __getParamCrop($clipTo)
    {
        $sub_start = strpos($clipTo, 'rect(');
        $sub_end = strpos($clipTo, ');');
        $start = $sub_start + strlen('rect(');
        $length = ($sub_end - 5) - $sub_start;
        $sub_str = substr($clipTo, $start, $length);
        return explode(',', $sub_str);
    }

    private function _getCoordinates($data, $target, $widthData, $heightData)
    {
        $targetWidthScale = round($target->width * $target->scaleX);
        $targetHeightScale = round($target->height * $target->scaleY);

        $coordinates = new \stdClass();
        $coordinates->left = $data->left;
        $coordinates->top = $data->top;
        $hypot = round(hypot($widthData, $heightData) / 2, 2);
        $beta = $widthData ? rad2deg(atan($heightData / $widthData)) : 0;
        $angle = $data->angle;

        if (($angle >= 0 && $angle < (90 - $beta)) || ($angle >= (360 - $beta) && $angle < 360)) {
            $sides = $this->_calSides(90 - $beta - $angle, $hypot);
            $coordinates->left = $coordinates->left + $sides->adjacent - ($target->left + $targetWidthScale * 0.5);
            $coordinates->top = -$coordinates->top - $sides->opposite + ($target->top + $targetHeightScale * 0.5);
        }

        if ($angle >= (90 - $beta) && $angle < (180 - $beta)) {
            $sides = $this->_calSides($angle - 90 + $beta, $hypot);
            $coordinates->left = $coordinates->left - $sides->adjacent - ($target->left + $targetWidthScale * 0.5);
            $coordinates->top = -$coordinates->top - $sides->opposite + ($target->top + $targetHeightScale * 0.5);
        }

        if ($angle >= (180 - $beta) && $angle < (270 - $beta)) {
            $sides = $this->_calSides($angle - 180 + $beta, $hypot);
            $coordinates->left = $coordinates->left - $sides->opposite - ($target->left + $targetWidthScale * 0.5);
            $coordinates->top = -$coordinates->top + $sides->adjacent + ($target->top + $targetHeightScale * 0.5);
        }

        if ($angle >= (270 - $beta) && $angle < (360 - $beta)) {
            $sides = $this->_calSides($angle - 270 + $beta, $hypot);
            $coordinates->left = $coordinates->left + $sides->adjacent - ($target->left + $targetWidthScale * 0.5);
            $coordinates->top = -$coordinates->top + $sides->opposite + ($target->top + $targetHeightScale * 0.5);
        }

        return $coordinates;
    }

    private function _calSides($angle, $hypot)
    {
        $sides = new \stdClass();
        $sides->adjacent = round(sin(deg2rad($angle)) * $hypot, 2);
        $sides->opposite = sqrt(pow($hypot, 2) - pow($sides->adjacent, 2));

        return $sides;
    }

    public function _getIndoorByTarget($target)
    {
        $indoor = $target->arIndoor->first();
        if (!$indoor) {
            return [];
        }
        return [
            'hotspotList' => $indoor->places()->with(['images'])->get(),
            'routeList' => json_decode($indoor->routes),
            'matrix' => $indoor->matrix,
            'latitude' => $indoor->pivot->latitude,
            'longitude' => $indoor->pivot->longitude,
        ];
    }
}
