<?php


namespace App\Http\Controllers;


use App\Http\Transformers\PostTransformer;
use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\Tagged;
use App\Services\BaseService;
use App\Services\MedialImageableService;
use App\Services\PostService;
use App\Services\StorageService;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use Throwable;

/**
 * @OA\Schema(
 *   schema="PostSchema",
 *   title="Post Model",
 *   description="Post model",
 *   @OA\Property(
 *     property="id", description="ID of the Post",
 *     @OA\Schema(type="number", example=1)
 *  ),
 *   @OA\Property(
 *     property="name", description="name of the Post",
 *     @OA\Schema(type="string", example="")
 *  ),
 *  @OA\Property(
 *     property="description", description="description of the Post",
 *     @OA\Schema(type="string", example="")
 *  ),
 *  @OA\Property(
 *     property="origin", description="origin of the Post",
 *     @OA\Schema(type="string", example="")
 *  ),
 *  @OA\Property(
 *     property="status", description="status of the Post",
 *     @OA\Schema(type="string", example="")
 *  )
 * )
 */
class PostController extends Controller
{
    /**
     * Create a new CategoryController instance.
     *
     * @return void
     */
    private $medialImageableService;
    private $postService;

    public function __construct(Manager $manager)
    {
        //$this->middleware('auth');
        $this->postService = new PostService();
        $this->manager = $manager;
        $this->medialImageableService = new MedialImageableService();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/post",
     *     operationId="/api/v1/post",
     *     tags={"Post"},
     *     @OA\Parameter(
     *         name="lang",
     *         in="query",
     *         description="The lang parameter in query, if not fill this param lang = vi",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="The type parameter in query, value in list (general, news) if not fill this param type = news",
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
     *         name="postId",
     *         in="query",
     *         description="The postId parameter in query, if fill data will return related post",
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
     *                                      "display_order": 0,
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
            //DB::enableQueryLog();
            $lang = $request->get('lang') ? $request->get('lang') : 'vi';
            app()->setLocale($lang);
            $posts = $this->postService->getPostBuilder();
            $type = $request->get('type') ? $request->get('type') : 'news';
            $search = $request->all();

            $posts = $posts->where('type', $type);
            $pagination = null;
            if ($type == 'news') {
                $posts = self::_searchWithParams($posts, $search);
                $posts = $posts->orderBy('post_date', 'desc');
                $baseService = new BaseService();
                $paginationParams = $baseService->getPaginationParams($request);
                $pagination = $baseService->getPaginationData($posts, $paginationParams['page'], $paginationParams['limit']);

                $posts = $posts->offset($paginationParams['offset'])->limit($paginationParams['limit'])->get();
            } else {
                $posts = $posts->orderBy('display_order', 'asc')->get();
            }
            $storageService = new StorageService();
            foreach ($posts as $post) {
                $avatar = null;
                $images = [];
                $medias = $this->medialImageableService->findByManyWithObject(null, Post::class, ['*'], $post->id, true);
                if ($medias) {
                    foreach ($medias as $media) {
                        if ($media->zone == 'avatar') {
                            $avatar = $storageService->url($media->file->path);
                        } else {
                            $images[] = $storageService->url($media->file->path);
                        }
                    }
                }
                $post->avatar = $avatar;
                $post->images = $images;

            }
            $formatPost = new Collection($posts, new PostTransformer());
            $formatPost = $this->manager->createData($formatPost)->toArray();
            $formatPost = $formatPost['data'];
            return $this->_successResponse($formatPost, Lang::get('messages.success'), $pagination);
        } catch (Throwable $t) {
            Log::info($t->getMessage());
            return $this->_errorResponse($t->getMessage());
        }
    }

    private function _searchWithParams($query, $search)
    {
        if (isset($search['keyword']) && strlen($search['keyword'])) {
            $keyword = $this->escapeLike($search['keyword']);
            $keyword = utf8Convert($keyword);
            $translations = PostTranslation::query();
            $translations = $translations->where(function ($subQuery) use ($keyword) {
                $subQuery->whereRaw("LOWER(unaccent(title)) LIKE '%" . (trim(strtolower($keyword))) . "%'")
                    ->orWhereRaw("LOWER(unaccent(short_desc)) LIKE '%" . (trim(strtolower($keyword))) . "%'");
//                    ->orWhereRaw("LOWER(unaccent(description)) LIKE '%" . (trim(strtolower($keyword))) . "%'");
            });

            $translationsIds = $translations->get()->pluck('post_id');
            $query->whereIn('id', $translationsIds);
        }

        if (isset($search['postId']) && strlen($search['postId'])) {
            $postId = $search['postId'];
            $post = $this->postService->checkPost($postId);
            if ($post) {
                $tagIds = Tagged::query()->where('taggable_id', $post->id)->where('taggable_type', Post::class)->get()->pluck('tag_id')->toArray();
                $postIds = Tagged::query()->where('taggable_id', '!=', $post->id)->whereIn('tag_id', $tagIds)->where('taggable_type', Post::class)->get()->pluck('taggable_id')->toArray();
                $query->where(function ($subQuery) use ($postIds) {
                    $subQuery->whereIn('posts.id', $postIds);
                });
            }
        }
        return $query;
    }

    private static function escapeLike($str)
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $str);
    }
    /**
     * Get Post detail
     *
     * @param  $PostId
     *
     * @return JsonResponse
     */

    /**
     * @OA\Get(
     *     path="/api/v1/post/detail/{postId}",
     *     operationId="/api/v1/post/detail/{postId}",
     *     tags={"Post"},
     *     @OA\Parameter(
     *         name="lang",
     *         in="query",
     *         description="The lang parameter in query, if not fill this param lang = vi",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         description="The post id in path",
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
    public function detailPost($postId, Request $request)
    {
        try {
            $lang = $request->get('lang') ? $request->get('lang') : 'vi';
            app()->setLocale($lang);
            $post = $this->postService->getPostBuilder();
            $post = $post->where('id', $postId)->first();
            if ($post) {
                $storageService = new StorageService();
                $avatar = null;
                $images = [];
                $medias = $this->medialImageableService->findByManyWithObject(null, Post::class, ['*'], $post->id, true);
                if ($medias) {
                    foreach ($medias as $media) {
                        if ($media->zone == 'avatar') {
                            $avatar = $storageService->url($media->file->path);
                        } else {
                            $images[] = $storageService->url($media->file->path);
                        }
                    }
                }
                $post->avatar = $avatar;
                $post->images = $images;
                $formatPost = new Collection([$post], new PostTransformer());
                $formatPost = $this->manager->createData($formatPost)->toArray();
                $formatPost = $formatPost['data'][0];
                return $this->_successResponse($formatPost, Lang::get('messages.success'));
            } else {
                return $this->_errorResponse(Lang::get('errors.artifacts.not found'));
            }
        } catch (Throwable $t) {
            Log::info($t->getMessage());
            return $this->_errorResponse($t->getMessage());
        }
    }
}
