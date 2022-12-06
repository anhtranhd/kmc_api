<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Artifact;
use App\Models\ArtifactTranslation;
use App\Models\Tag;
use App\Models\Tagged;
use App\Models\Category;
use App\Services\ArtifactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use App\Http\Transformers\ArtifactTransformer;
use League\Fractal\Manager;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\MediaService;

class ArtifactController extends Controller
{
    private $artifactService;
    /**
     * Create a new BannerController instance.
     *
     * @return void
     */
    public function __construct(Manager $manager) {
        //$this->middleware('auth');
        parent::__construct();
        $this->artifactService = new ArtifactService();
        $this->manager = $manager;
    }


    public function getArtifacts(Request $request)
    {
        try {
            $lang = $request->get('lang') ? $request->get('lang') : 'vi' ;
            app()->setLocale($lang);
            $search = $request->all();
            $artifacts = $this->artifactService->getArtifactBuilder();
            $artifacts = self::_searchWithParams($artifacts, $search);

            $artifacts = self::applySortParams($request, $artifacts);

            $paginationParams = $this->baseService->getPaginationParams($request);

            $pagination = $this->baseService->getPaginationData($artifacts, $paginationParams['page'], $paginationParams['limit']);

            $artifacts = $artifacts->offset($paginationParams['offset'])->limit($paginationParams['limit'])->get();

            $formatartifacts = [];
            foreach($artifacts as $artifact) {
                $formatartifacts[] = $this->artifactService->transformArtifact($artifact);
            }
            return $this->_successResponse($formatartifacts, Lang::get('messages.success'), $pagination);
        } catch (Throwable $t) {
            Log::info($t->getMessage());
            return $this->_errorResponse($t->getMessage());
        }
    }
    private static function escapeLike($str) {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $str);
    }
    private static function applySortParams($request, $builder) {
        $sortParam = strtolower($request->get('sortParam', 'created_at'));
        $sortOrder  = strtolower($request->get('sortOrder', 'desc'));
        if (in_array($sortParam, ['suggestion', 'created_at']) && in_array($sortOrder, ['asc', 'desc'])) {
            switch ($sortParam) {
                case 'suggestion':
                    $field = "artifacts.created_at";
                    break;
                default:
                    $field = 'artifacts.created_at';
                    break;
            }
            if ($field != null) {
                $builder->orderBy($field, $sortOrder);
            }
        }

        return $builder;
    }
    private function _searchWithParams($query, $search) {
        if (isset($search['keyword']) && strlen($search['keyword'])) {
            $keyword = $this->escapeLike($search['keyword']);
            $translations = ArtifactTranslation::query();
            $keyword = utf8Convert($keyword);
            $translations = $translations->where(function ($subQuery) use ($keyword) {
                $subQuery->whereRaw("LOWER(unaccent(name)) LIKE '%" . utf8Convert(trim(strtolower($keyword))) . "%'");
            });
            $translationsIds = $translations->get()->pluck('artifact_id');
            $query = $query->where(function ($query) use ($keyword, $translationsIds) {
                $query->whereIn('artifacts.id', $translationsIds)
                    ->orWhereRaw("LOWER(unaccent(code)) LIKE '%" . utf8Convert(trim(strtolower($keyword))) . "%'");
                return $query;
            });
        }

        if (isset($search['categoryIds']) && !empty($search['categoryIds'])) {
            $categoryIds = $search['categoryIds'];
            $idCategories = [];
            foreach ($categoryIds as $categoryId) {
                $idCategories[] = (int)$categoryId;
                $category = Category::find($categoryId);
                if ($category) {
                    $hierarchy = $category->parent_id == Category::PARENT_ID ? "/{$category->id}/" : $category->hierarchy;
                    $childrenCategory = Category::query()->where('hierarchy', 'like', $hierarchy . '%')->get()->pluck('id');
                    $idCategories = array_merge($idCategories, $childrenCategory->toArray());
                }
            }
            $query->where(function ($subQuery) use ($idCategories) {
                $subQuery->whereIn('artifacts.category_id', $idCategories);
            });
        }

        if (isset($search['showroomIds']) && !empty($search['showroomIds'])) {
            $showroomIds = $search['showroomIds'];
            $idShowroom = [];
            foreach ($showroomIds as $showroomId) {
                $idShowroom[] = (int)$showroomId;
            }
            $query->where(function ($subQuery) use ($idShowroom) {
                $subQuery->whereIn('artifacts.showroom_id', $idShowroom);
            });
        }

        if (isset($search['status'])) {
            $status = $search['status'];
                $query->where('status', $status);
        }

        if (isset($search['artifact_id']) && strlen($search['artifact_id'])) {
            $artifactId = $search['artifact_id'];
            $artifact = $this->artifactService->checkartifact($artifactId);
            if ($artifact) {
                $category_id = $artifact->category_id;
                $query->where(function ($subQuery) use ($artifactId, $category_id) {
                    $subQuery->where('artifacts.id', '!=' , $artifactId)->where('artifacts.category_id', $category_id);
                });
            }
        }
        return $query;
    }
    public function detailArtifact($id, Request $request)
    {
        try {
            $lang = $request->get('lang') ? $request->get('lang') : 'vi' ;
            app()->setLocale($lang);
            $artifact = $this->artifactService->getartifactBuilder();
            $artifact = $artifact->where('id', $id)->first();
            if ($artifact) {
                $artifact = $this->artifactService->transformArtifact($artifact);
                return $this->_successResponse($artifact, Lang::get('messages.success'));
            } else {
                return $this->_errorResponse(Lang::get('errors.artifacts.not found'));
            }
        } catch (Throwable $t) {
            Log::info($t->getMessage());
            return $this->_errorResponse(Lang::get('messages.system error'));
        }
    }

    public function detailArtifactByCode(Request $request)
    {
        try {
            $code = $request->get('code');
            if (!$code) {
                return $this->_errorResponse(Lang::get('errors.artifacts.code is require'));
            }
            $lang = $request->get('lang') ? $request->get('lang') : 'vi' ;
            app()->setLocale($lang);
            $artifact = $this->artifactService->getartifactBuilder();
            $artifact = $artifact->where('code', $code)->first();
            if ($artifact) {
                $artifact = $this->artifactService->transformArtifact($artifact);
                return $this->_successResponse($artifact, Lang::get('messages.success'));
            } else {
                return $this->_errorResponse(Lang::get('errors.artifacts.not found'));
            }
        } catch (Throwable $t) {
            Log::info($t->getMessage());
            return $this->_errorResponse(Lang::get('messages.system error'));
        }
    }
}
