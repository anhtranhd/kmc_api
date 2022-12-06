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
