<?php

namespace App\Services;

use App\Models\ArContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

use Illuminate\Contracts\Validation\Validator as ReturnedValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Lang;
use IonGhitun\JwtToken\Jwt;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;

/**
 * Class ArContentService
 *
 * @package App\Services
 */
class ArContentService
{
    /**
     * Validate request on login
     *
     * @param  Request  $request
     *
     * @return ReturnedValidator
     */
    public function validateDetailArContentRequest(Request $request)
    {
        $rules = [
            'lang' => 'nullable',
            'target_id' => 'required',
            'type' => 'required|in:indoor,content,brand'
        ];

        return Validator::make($request->all(), $rules);
    }
    /**
     * Get banner builder.
     *
     * @return Banner|Builder
     */
    public function getBannersBuilder()
    {

        $banners = Banner::with('medialImageable');

        $banners = $banners->where('status', Banner::$STATUS_ACTIVE);

        return $banners;
    }
}
