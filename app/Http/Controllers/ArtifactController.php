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

    /**
     * Get artifacts
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/v1/artifacts",
     *     operationId="/api/v1/artifacts",
     *     tags={"Artifacts"},
     *     @OA\Parameter(
     *         name="lang",
     *         in="query",
     *         description="The lang parameter in query, if not fill this param lang = vi",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="The keyword parameter in query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="categoryIds[]",
     *         in="query",
     *         description="list category id for filter",
     *         required=false,
     *         @OA\Schema(
     *         type="array",
     *           @OA\Items(
     *               type="integer"
     *           ),
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="showroomIds[]",
     *         in="query",
     *         description="list showroom id for filter",
     *         required=false,
     *         @OA\Schema(
     *         type="array",
     *           @OA\Items(
     *               type="integer"
     *           ),
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="artifact_id",
     *         in="query",
     *         description="The artifact id parameter in query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="The status parameter in query. Only use for storer, value in (0, 1) ",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sortParam",
     *         in="query",
     *         description="The sortParam parameter in query, value in list 'suggestion' - phù hợp nhất, 'created_at'- mới nhất, 'price' - giá, 'has_ar' - có ar",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sortOrder",
     *         in="query",
     *         description="The sortOrder parameter in query, sortOrder = 'asc' to get list artifact với sortParam theo asc, sortOrder = 'desc' to get artifact với sortParam theo desc",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="The page parameter in query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="The limit parameter in path",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="ok",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                      @OA\Property(
     *                         property="success",
     *                         type="boolean",
     *                         description="The response error status"
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="The response message"
     *                     ),
     *                     @OA\Property(
     *                         property="data",
     *                         type="array",
     *                         description="The response data",
     *                         @OA\Items
     *                     ),
     *                     example={
     *                          "success": false,
     *                         "message": "success",
     *                         "data": {
     *                                  {
     *                                      "id": 111,
     *                                      "name": "Ghế sofa victorian",
     *                                      "sku": "SKU222222",
     *                                      "attributes": {},
     *                                      "categoryId": 107,
     *                                      "categoryName": "Ghế",
     *                                      "storeName": "Breanna Emmerich Jr.",
     *                                      "brandName": null,
     *                                      "description": "<p>Ghế sofa</p>",
     *                                      "price": "",
     *                                      "quantity": "",
     *                                      "origin": "",
     *                                      "totalComments": 0,
     *                                      "avgRating": "0.0",
     *                                      "avgStar": "<span class=\'fas fa-star \'></span><span class=\'fas fa-star \'></span><span class=\'fas fa-star \'></span><span class=\'fas fa-star \'></span><span class=\'fas fa-star \'></span>",
     *                                      "images": {},
     *                                      "video": null,
     *                                      "threeDimensional": null,
     *                                      "countStar": {
     *                                          "1": 0,
     *                                          "2": 0,
     *                                          "3": 0,
     *                                          "4": 0,
     *                                          "5": 0
     *                                      },
     *                                      "countComment": 0,
     *                                      "countMedia": 0
     *                                 }
     *                          },
     *                          "pagination": {
     *                              "currentPage": 1,
     *                              "totalPages": 2,
     *                              "limit": 10,
     *                              "totalResult": 15
     *                          }
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * )
     */
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
    /**
     * Get artifact detail
     *
     * @param  $artifactId
     *
     * @return JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/v1/artifacts/detail/{id}",
     *     operationId="/api/v1/artifacts/detail/{id}",
     *     tags={"Artifacts"},
     *     @OA\Parameter(
     *         name="lang",
     *         in="query",
     *         description="The lang parameter in query, if not fill this param lang = vi",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The artifact id in path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="ok",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                      @OA\Property(
     *                         property="success",
     *                         type="boolean",
     *                         description="The response error status"
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="The response message"
     *                     ),
     *                     @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         description="The response result"
     *                     ),
     *                     example={
     *                          "success": false,
     *                         "message": "success",
     *                         "data":{
     *                                      "id": 111,
     *                                      "name": "Ghế sofa victorian",
     *                                      "sku": "SKU222222",
     *                                      "attributes": {},
     *                                      "categoryId": 107,
     *                                      "categoryName": "Ghế",
     *                                      "storeName": "Breanna Emmerich Jr.",
     *                                      "brandName": null,
     *                                      "description": "<p>Ghế sofa</p>",
     *                                      "price": "",
     *                                      "quantity": "",
     *                                      "origin": "",
     *                                      "totalComments": 0,
     *                                      "avgRating": "0.0",
     *                                      "avgStar": "<span class=\'fas fa-star \'></span><span class=\'fas fa-star \'></span><span class=\'fas fa-star \'></span><span class=\'fas fa-star \'></span><span class=\'fas fa-star \'></span>",
     *                                      "images": {},
     *                                      "video": null,
     *                                      "threeDimensional": null,
     *                                      "countStar": {
     *                                          "1": 0,
     *                                          "2": 0,
     *                                          "3": 0,
     *                                          "4": 0,
     *                                          "5": 0
     *                                      },
     *                                      "countComment": 0,
     *                                      "countMedia": 0
     *                                }
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * )
     */
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

    /**
     * Get artifact detail by code
     *
     * @param  $code
     *
     * @return JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/v1/artifacts/detail-by-code",
     *     operationId="/api/v1/artifacts/detail-by-code",
     *     tags={"Artifacts"},
     *     @OA\Parameter(
     *         name="lang",
     *         in="query",
     *         description="The lang parameter in query, if not fill this param lang = vi",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="code",
     *         in="query",
     *         description="The artifact code in path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="ok",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                      @OA\Property(
     *                         property="success",
     *                         type="boolean",
     *                         description="The response error status"
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="The response message"
     *                     ),
     *                     @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         description="The response result"
     *                     ),
     *                     example={
     *                          "success": false,
     *                         "message": "success",
     *                         "data":{
     *                                      "id": 111,
     *                                      "name": "Ghế sofa victorian",
     *                                      "sku": "SKU222222",
     *                                      "attributes": {},
     *                                      "categoryId": 107,
     *                                      "categoryName": "Ghế",
     *                                      "storeName": "Breanna Emmerich Jr.",
     *                                      "brandName": null,
     *                                      "description": "<p>Ghế sofa</p>",
     *                                      "price": "",
     *                                      "quantity": "",
     *                                      "origin": "",
     *                                      "totalComments": 0,
     *                                      "avgRating": "0.0",
     *                                      "avgStar": "<span class=\'fas fa-star \'></span><span class=\'fas fa-star \'></span><span class=\'fas fa-star \'></span><span class=\'fas fa-star \'></span><span class=\'fas fa-star \'></span>",
     *                                      "images": {},
     *                                      "video": null,
     *                                      "threeDimensional": null,
     *                                      "countStar": {
     *                                          "1": 0,
     *                                          "2": 0,
     *                                          "3": 0,
     *                                          "4": 0,
     *                                          "5": 0
     *                                      },
     *                                      "countComment": 0,
     *                                      "countMedia": 0
     *                                }
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * )
     */
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
