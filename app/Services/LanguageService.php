<?php

namespace App\Services;

use App\Models\Language;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Validation\Validator as ReturnedValidator;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Throwable;
use SplFileInfo;
use URL;
use App\Services\StorageService;

/**
 * Class LanguageService
 *
 * @package App\Services
 */
class LanguageService extends BaseService
{
    /**
     * Get Language builder.
     *
     * @return Language|Builder
     */
    public function getLanguageBuilder()
    {

        $languages = Language::query()->where('status', STATUS_ACTIVE)->whereNull('deleted_at');
        return $languages;
    }

    /**
     * Get Language builder.
     *
     * @return Language|Builder
     */
    public function checkLanguage($id)
    {

        $language = Language::query()->where('id', $id)->where('status', STATUS_ACTIVE)->whereNull('deleted_at')->first();

        return $language;
    }
}
