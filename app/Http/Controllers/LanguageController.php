<?php


namespace App\Http\Controllers;


use App\Models\Language;
use App\Services\ButtonService;
use App\Services\LanguageService;
use App\Services\StorageService;
use App\Services\MedialImageableService;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * @OA\Schema(
 *   schema="LanguageSchema",
 *   title="Language Model",
 *   description="Language model",
 *   @OA\Property(
 *     property="id", description="ID of the Language",
 *     @OA\Schema(type="number", example=1)
 *  ),
 *   @OA\Property(
 *     property="name", description="name of the Language",
 *     @OA\Schema(type="string", example="")
 *  ),
 *  @OA\Property(
 *     property="description", description="description of the Language",
 *     @OA\Schema(type="string", example="")
 *  ),
 *  @OA\Property(
 *     property="origin", description="origin of the Language",
 *     @OA\Schema(type="string", example="")
 *  ),
 *  @OA\Property(
 *     property="status", description="status of the Language",
 *     @OA\Schema(type="string", example="")
 *  )
 * )
 */
class LanguageController extends Controller
{
    /**
     * Create a new CategoryController instance.
     *
     * @return void
     */
    private $medialImageableService;
    /**
     * @var LanguageService
     */
    private $languageService;
    /**
     * @var ButtonService
     */
    private $buttonService;

    public function __construct()
    {
        //$this->middleware('auth');
        $this->languageService = new LanguageService();
        $this->buttonService = new ButtonService();
        $this->medialImageableService = new MedialImageableService();
    }

    /**
     * @OA\Get(
     *     path="/api/language",
     *     operationId="/api/language",
     *     tags={"Languages"},
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
     *                                      "code": "en",
     *                                      "language": "English",
     *                                      "isRTL": 1,
     *                                      "labels": {
     *                                          {
     *                                              "id": 1,
     *                                              "dom_id": "button_1",
     *                                              "dom_html": "Save"
     *                                          },
     *                                          {
     *                                              "id": 2,
     *                                              "dom_id": "button_2",
     *                                              "dom_html": "Cancel"
     *                                          }
     *                                      }
     *                                  }
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
    public function getListLanguage()
    {
        try {
            $langs = $this->languageService->getLanguageBuilder();
            $langs = $langs->orderBy('priority', 'asc')->get();
            $data = [];
            $storageService = new StorageService();
            foreach ($langs as $lang) {
                $obj = new \stdClass();
                $image = null;
                $media = $this->medialImageableService->findByManyWithObject(null, Language::class, ['*'], $lang->id);
                if ($media && $media->file) {
                    $image = $storageService->url($media->file->path);
                }
                $obj->image = $image;
                $obj->code = $lang->google_code;
                $obj->language = $lang->en_name;
                $obj->isRTL = $lang->is_rtl;
                $buttonData = $this->buttonService->getButtonByCode($lang->google_code);
                $obj->labels = $buttonData;
                $data[] = $obj;
            }
            return $this->_successResponse($data, Lang::get('messages.success'));
        } catch (Throwable $t) {
            Log::info($t->getMessage());
            return $this->_errorResponse($t->getMessage());
        }
    }

}
