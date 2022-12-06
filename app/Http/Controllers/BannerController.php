<?php

namespace App\Http\Controllers;

use App\Constants\TranslationCode;
use App\Models\User;
use App\Models\Banner;
use App\Services\BannerService;
use App\Services\MedialImageableService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\ConfigHelper;
use League\Fractal\Resource\Collection;
use App\Http\Transformers\BannerTransformer;
use League\Fractal\Manager;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Lang;
use App\Services\StorageService;
use DB;
/**
 * @OA\Schema(
 *   schema="BannerSchema",
 *   title="Banner Model",
 *   description="CateBannergory model",
 *   @OA\Property(
 *     property="id", description="ID of the Banner",
 *     @OA\Schema(type="number", example=1)
 *  ),
 *   @OA\Property(
 *     property="name", description="name of the Banner",
 *     @OA\Schema(type="string", example="admin_name")
 *  )
 * )
 */
class BannerController extends Controller
{
    private $bannerService;
    private $medialImageableService;
    /**
     * Create a new BannerController instance.
     *
     * @return void
     */
    public function __construct(BannerTransformer $bannerTransformer, Manager $manager) {
        //$this->middleware('auth');
        parent::__construct();
        $this->bannerService = new BannerService();
        $this->bannerTransformer = $bannerTransformer;
        $this->manager = $manager;
        $this->medialImageableService = new MedialImageableService();
    }

    /**
     * Get banner
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/v1/banner",
     *     operationId="/api/v1/banner",
     *     tags={"Banners"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="The type parameter in query, type = 'mobile' to get list banner for mobile, type = 'web' to get banner for web",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="The page parameter in path",
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
     *                          "success": true,
     *                         "message": "success",
     *                         "data": {
     *                                  {
     *                                      "id": 1,
     *                                      "parent_id": null,
     *                                      "name": "Nội thất",
     *                                      "description": null,
     *                                      "link": "Nội thất",
     *                                      "order": 0,
     *                                      "status": 1,
     *                                      "created_at": null,
     *                                      "updated_at": null,
     *                                      "image": "http://localhost:8000/images/img-default.png"
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
    public function getBanners(Request $request)
    {
        try {
            $banners = $this->bannerService->getBannersBuilder($request);
            $banners = $banners->offset(0)->limit(5)->get();
            $storageService = new StorageService();
            foreach ($banners as $banner) {
                $image = asset('images/img-default.png');
                $media = $this->medialImageableService->findByManyWithObject(null, Banner::class, ['*'], $banner->id);
                if ($media) {
                    $image = $storageService->url($media->file->path);
                }
                $banner->image = $image;
            }
            $formatBanner = new Collection($banners, new BannerTransformer());
            $formatBanner = $this->manager->createData($formatBanner)->toArray();
            $formatBanner = $formatBanner['data'];
            return $this->_successResponse($formatBanner, Lang::get('messages.success'));
        } catch (Throwable $t) {
            Log::info($t->getMessage());
            return $this->_errorResponse(Lang::get('messages.system error'));
        }
    }
}
