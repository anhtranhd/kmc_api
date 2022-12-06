<?php

namespace App\Services;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Class BannerService
 *
 * @package App\Services
 */
class BannerService
{
    /**
     * Get banner builder.
     *
     * @return Banner|Builder
     */
    public function getBannersBuilder()
    {

        $banners = Banner::query();

        $banners = $banners->where('status', Banner::$STATUS_ACTIVE)->orderBy('order');

        return $banners;
    }
}
