<?php

namespace App\Services;

use App\Models\MedialImageable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class MediaService
 *
 * @package App\Services
 */
class MedialImageableService
{
//    public function findByManyWithObject($ids = null, $model, $columns = ['*'], $modelId = null, $all = false)
    public function findByManyWithObject($ids , $model, $columns = ['*'], $modelId = null, $all = false)
    {
        $query = MedialImageable::query();
        $query->select($columns)->with('file');

        if ($ids !== null) {
            $query->whereIn('id', $ids);
        }
        if ($modelId) {
            $query->where('imageable_id', $modelId);
        }
        $query->where('imageable_type', $model);
        if ($modelId && !$all) {
            return $query->first();
        }
        return $query->get();
    }

    public function deleteByObject($model)
    {
        $query = MedialImageable::query();
        return $query->where([
            ['imageable_id', $model->id],
            ['imageable_type', get_class($model)]
        ])->delete();
    }
	public function deleteByObjectAndZone($model, $zone)
	{
        $query = MedialImageable::query();
		return $query->where([
			['imageable_id', $model->id],
			['zone', $zone],
			['imageable_type', get_class($model)]
		])->delete();
	}
    public function deleteByFileIds($ids)
    {
        $query = MedialImageable::query();
        return $query->whereIn('file_id', $ids)->delete();
    }
}
