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
