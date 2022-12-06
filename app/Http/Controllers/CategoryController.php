<?php


namespace App\Http\Controllers;


use App\Http\Transformers\ArtifactTransformer;
use App\Http\Transformers\CategoryTransformer;
use App\Models\Artifact;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Services\ArtifactService;
use App\Services\BaseService;
use App\Services\CategoryService;
use App\Services\MedialImageableService;
use App\Services\StorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use Throwable;

/**
 * @OA\Schema(
 *   schema="CategorySchema",
 *   title="Category Model",
 *   description="Category model",
 *   @OA\Property(
 *     property="id", description="ID of the user",
 *     @OA\Schema(type="number", example=1)
 *  ),
 *   @OA\Property(
 *     property="user_name", description="user_name of the user",
 *     @OA\Schema(type="string", example="admin_name")
 *  )
 * )
 */
class CategoryController extends Controller
{
    /**
     * Create a new CategoryController instance.
     *
     * @return void
     */
    private $medialImageableService;
    private $categoryService;
    private $artifactService;
    private $manager;

    public function __construct(Manager $manager, CategoryService $categoryService,
                                MedialImageableService $medialImageableService,
                                ArtifactService $artifactService)
    {
        $this->categoryService = $categoryService;
        $this->manager = $manager;
        $this->medialImageableService = $medialImageableService;
        $this->artifactService = $artifactService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/categories",
     *     operationId="/api/v1/categories",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="The category id parameter in query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
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
     *                         description="The response result",
     *                         @OA\Items
     *                     ),
     *                     example={
     *                         "success": true,
     *                         "message": "success",
     *                         "data": {
     *                                  {
     *                                      "id": 1,
     *                                      "parent_id": null,
     *                                      "name": "Nội thất",
     *                                      "description": null,
     *                                      "hierarchy": "Nội thất",
     *                                      "order": 0,
     *                                      "status": 1,
     *                                      "created_by": null,
     *                                      "updated_by": null,
     *                                      "created_at": null,
     *                                      "updated_at": null
     *                                  }
     *
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
    public function index(Request $request)
    {
        try {
            $lang = $request->get('lang') ? $request->get('lang') : 'vi';
            app()->setLocale($lang);
            $categories = $this->categoryService->getCategoryBuilder();

            if ($request->get('keyword')) {
                $keyword = $this->escapeLike($request->get('keyword'));
                $keyword = utf8Convert($keyword);
                $translations = CategoryTranslation::query();
                $translations->where(function ($subQuery) use ($keyword) {
                    $subQuery->whereRaw("LOWER(unaccent(name)) LIKE '%" . (trim(strtolower($keyword))) . "%' ")
                        ->orWhereRaw("LOWER(unaccent(description)) LIKE '%" . (trim(strtolower($keyword))) . "%'");
                });
                $translations->where('locale', "=", $lang);
                $translationsIds = $translations->get()->pluck('category_id');
                $categories = $categories->whereIn('id', $translationsIds);
            }

            if ($request->get('category_id')) {
                $categories = $categories->where('parent_id', (int)$request->get('category_id'))->orderBy('order', 'desc');
            } else {
                $categories = $categories->where('parent_id', 0)->orderBy('order', 'desc');
            }

            $baseService = new BaseService();
            $paginationParams = $baseService->getPaginationParams($request);

            $pagination = $baseService->getPaginationData($categories, $paginationParams['page'], $paginationParams['limit']);

            $categories = $categories->offset($paginationParams['offset'])->limit($paginationParams['limit'])->get();

            $storageService = new StorageService();
            foreach ($categories as $category) {
                $image = null;
                $media = $this->medialImageableService->findByManyWithObject(null, Category::class, ['*'], $category->id);
                if ($media) {
                    $image = $storageService->url($media->file->path);
                }
                $category->image = $image;
                $checkHasChild = $this->categoryService->getCategoryBuilder()->where('parent_id', $category->id)->count();
                $category->hasChild = $checkHasChild > 0 ? true : false;
                $totalArtifact = $this->artifactService->getTotalArtifactByCategoryId($category->id);
                $category->totalArtifact = $totalArtifact;
            }
            $formatCategories = new Collection($categories, new CategoryTransformer());
            $formatCategories = $this->manager->createData($formatCategories)->toArray();
            $formatCategories = $formatCategories['data'];
            return $this->_successResponse($formatCategories, Lang::get('messages.success'), $pagination);
        } catch (Throwable $t) {
            Log::info($t->getMessage());
            return $this->_errorResponse($t->getMessage());
        }
    }

    private static function escapeLike($str)
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $str);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/categories/tree",
     *     operationId="/api/v1/categories/tree",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="lang",
     *         in="query",
     *         description="The lang parameter in query, if not fill this param lang = vi",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="categoryId",
     *         in="query",
     *         description="The categoryId parameter in query",
     *         required=false,
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
     *                         type="array",
     *                         description="The response result",
     *                         @OA\Items
     *                     ),
     *                     example={
     *                         "success": true,
     *                         "message": "success",
     *                         "data": {
     *                                  {
     *                                      "id": 1,
     *                                      "parent_id": null,
     *                                      "name": "Nội thất",
     *                                      "description": null,
     *                                      "hierarchy": "Nội thất",
     *                                      "order": 0,
     *                                      "status": 1,
     *                                      "created_by": null,
     *                                      "updated_by": null,
     *                                      "created_at": null,
     *                                      "updated_at": null
     *                                  }
     *
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
    public function getListTree(Request $request)
    {
        try {
            $lang = $request->get('lang') ? $request->get('lang') : 'vi';
            app()->setLocale($lang);
            $categoryId = $request->get('categoryId');
            $categories = $this->categoryService->getCategoryAllTree($categoryId);
            $formatCategories = [];
            $storageService = new StorageService();
            foreach ($categories as $category) {
                $image = null;
                $media = $this->medialImageableService->findByManyWithObject(null, Category::class, ['*'], $category['id']);
                if ($media) {
                    $image = $storageService->url($media->file->path);
                }
                $category['image'] = $image;
                $checkHasChild = $this->categoryService->getCategoryBuilder()->where('parent_id', $category['id'])->count();
                $category['hasChild'] = $checkHasChild > 0 ? true : false;
                if ($category['hasChild'] && isset($category['children'])) {
                    foreach ($category['children'] as &$children) {
                        $image = null;
                        $media = $this->medialImageableService->findByManyWithObject(null, Category::class, ['*'], $children['id']);
                        if ($media) {
                            $image = $storageService->url($media->file->path);
                        }
                        $children['image'] = $image;
                        unset($children['children']);
                    }
                }
                $artifacts = Artifact::where('category_id', $category['id'])->where('status', STATUS_ACTIVE)->get();
                if ($artifacts){
                    $a = new ArtifactService();
                    $formatartifacts = [];
                    foreach($artifacts as $artifact) {
                        $formatartifacts[] = $a->transformArtifact($artifact);
                    }

                }
                $item = [
                    "id" => (int)$category['id'],
                    "name" => $category['name'],
                    "description" => $category['description'],
                    "hasChild" => $category['hasChild'],
                    "children" => isset($category['children']) ? $category['children'] : [],
                    "artifacts" => isset($formatartifacts) ? $formatartifacts : [],
                    "order" => $category['order'],
                    "status" => $category['status'],
                    "image" => !empty($category['image']) ? $category['image'] : '',
                    "createdAt" => (string)$category['created_at'],
                    "updatedAt" => (string)$category['updated_at']
                ];
                $formatCategories[] = (object)$item;
            }
            return $this->_successResponse($formatCategories, Lang::get('messages.success'));
        } catch (Throwable $t) {
            Log::info($t->getMessage());
            return $this->_errorResponse($t->getMessage());
        }
    }
}
