<?php


namespace App\Http\Controllers;


use App\Models\Showroom;
use App\Models\ShowroomTranslation;
use Illuminate\Support\Facades\Auth;
use App\Services\ShowroomService;
use App\Services\ArtifactService;
use League\Fractal\Resource\Collection;
use App\Http\Transformers\ShowroomTransformer;
use App\Services\MedialImageableService;
use League\Fractal\Manager;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Lang;
use App\Services\StorageService;
use Illuminate\Http\Request;
use App\Services\BaseService;

/**
 * @OA\Schema(
 *   schema="ShowroomSchema",
 *   title="Showroom Model",
 *   description="Showroom model",
 *   @OA\Property(
 *     property="id", description="ID of the Showroom",
 *     @OA\Schema(type="number", example=1)
 *  ),
 *   @OA\Property(
 *     property="name", description="name of the Showroom",
 *     @OA\Schema(type="string", example="")
 *  ),
 *  @OA\Property(
 *     property="description", description="description of the Showroom",
 *     @OA\Schema(type="string", example="")
 *  ),
 *  @OA\Property(
 *     property="origin", description="origin of the Showroom",
 *     @OA\Schema(type="string", example="")
 *  ),
 *  @OA\Property(
 *     property="status", description="status of the Showroom",
 *     @OA\Schema(type="string", example="")
 *  )
 * )
 */
class ShowroomController extends Controller
{
    /**
     * Create a new CategoryController instance.
     *
     * @return void
     */
    private $medialImageableService;
    private $artifactService;
    public function __construct(Manager $manager, ArtifactService $artifactService) {
        //$this->middleware('auth');
        $this->showroomService = new ShowroomService();
        $this->manager = $manager;
        $this->medialImageableService = new MedialImageableService();
        $this->artifactService = $artifactService;
    }
    /**
     * @OA\Get(
     *     path="/api/v1/showroom",
     *     operationId="/api/v1/showroom",
     *     tags={"Showrooms"},
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
            $lang = $request->get('lang') ? $request->get('lang') : 'vi' ;
            app()->setLocale($lang);
            $search = $request->all();
            $showrooms = $this->showroomService->getShowroomBuilder();

            $showrooms = self::_searchWithParams($showrooms, $search);

            $baseService = new BaseService();
            $paginationParams = $baseService->getPaginationParams($request);

            $pagination = $baseService->getPaginationData($showrooms, $paginationParams['page'], $paginationParams['limit']);

            $showrooms = $showrooms->offset($paginationParams['offset'])->limit($paginationParams['limit'])->orderBy('created_at', 'desc')->get();

            $formatShowrooms = new Collection($showrooms, new ShowroomTransformer());
            $formatShowrooms = $this->manager->createData($formatShowrooms)->toArray();
            $formatShowrooms = $formatShowrooms['data'];
            return $this->_successResponse($formatShowrooms, Lang::get('messages.success'), $pagination);
        } catch (Throwable $t) {
            Log::info($t->getMessage());
            return $this->_errorResponse($t->getMessage());
        }
    }

    public function getDetail($id)
    {
        try {
            $showrooms = Showroom::where('id', $id)->get();
            $formatShowrooms = new Collection($showrooms, new ShowroomTransformer());
            $formatShowrooms = $this->manager->createData($formatShowrooms)->toArray();
            $formatShowrooms = $formatShowrooms['data'];
            return $this->_successResponse($formatShowrooms, Lang::get('messages.success'));
        } catch (Throwable $t) {
            Log::info($t->getMessage());
            return $this->_errorResponse($t->getMessage());
        }
    }

    private static function escapeLike($str) {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $str);
    }
    private function _searchWithParams($query, $search) {
        if (isset($search['keyword']) && strlen($search['keyword'])) {
            $keyword = $this->escapeLike($search['keyword']);
            $keyword = utf8Convert($keyword);
            $translations = ShowroomTranslation::query();
            $translations = $translations->where(function ($subQuery) use ($keyword) {
                $subQuery->whereRaw("LOWER(unaccent(name)) LIKE '%" . utf8Convert(trim(strtolower($keyword))) . "%'")
                ->orWhereRaw("LOWER(unaccent(description)) LIKE '%" . utf8Convert(trim(strtolower($keyword))) . "%'");
            });
            $translationsIds = $translations->get()->pluck('showroom_id');
            $query = $query->where(function ($query) use ($keyword, $translationsIds) {
                $query->whereIn('id', $translationsIds)
                    ->orWhere('code', 'ilike', '%'. ($keyword) . '%');
                return $query;
            });
        }
        return $query;
    }

}
