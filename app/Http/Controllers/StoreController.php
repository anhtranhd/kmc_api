<?php

namespace App\Http\Controllers;

use App\Constants\TranslationCode;
use App\Models\User;
use App\Models\Store;
use App\Services\StoreService;
use App\Services\ArtifactService;
use App\Services\MedialImageableService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\ConfigHelper;
use League\Fractal\Resource\Collection;
use App\Http\Transformers\StoreTransformer;
use League\Fractal\Manager;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class StoreController extends Controller
{
    private $storeService;
    private $artifactService;
    private $medialImageableService;
    /**
     * Create a new BannerController instance.
     *
     * @return void
     */
    public function __construct(StoreTransformer $storeTransformer, Manager $manager) {
        //$this->middleware('auth');
        parent::__construct();
        $this->storeService = new StoreService();
        $this->artifactService = new ArtifactService();
        $this->storeTransformer = $storeTransformer;
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
    public function getStoreDetail($storeId)
    {
        try {
            $store = $this->storeService->getStoreBuilder();
            $store = $store->where('id', $storeId)->first();
            if ($store) {
                $totalartifactByStore = $this->artifactService->getTotalartifactByStore($storeId);
                $store->total_artifact = $totalartifactByStore;
                $store = $this->storeService->transformStore($store);
                return $this->_successResponse($store, Lang::get('messages.success'));
            } else {
                return $this->_errorResponse(Lang::get('errors.store.not found'));
            }
        } catch (Throwable $t) {
            Log::info($t->getMessage());
            return $this->_errorResponse(Lang::get('messages.system error'));
        }
    }
}
