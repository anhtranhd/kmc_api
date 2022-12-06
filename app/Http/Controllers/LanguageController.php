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
