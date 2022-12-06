<?php

namespace App\Services;

use App\Models\Showroom;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Class CategoryService
 *
 * @package App\Services
 */
class ShowroomService
{
    /**
     * Get Showroom builder.
     *
     * @return Showroom|Builder
     */
    public function getShowroomBuilder()
    {

        $showrooms = Showroom::query();

        $showrooms = $showrooms->where('status', Showroom::ACTIVE)->whereNull('deleted_at');

        return $showrooms;
    }
}
