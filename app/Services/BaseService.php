<?php

namespace App\Services;

use App\Models\Language;
use App\Models\RolePermission;
use App\Models\User;
use App\Models\Artifact;
use App\Models\Category;
use App\Models\Showroom;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use IonGhitun\MysqlEncryption\Models\BaseModel;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use App\Http\Transformers\ArtifactTransformer;
use App\Http\Transformers\ShowroomTransformer;
use App\Http\Transformers\CategoryTransformer;
use App\Http\Transformers\UserTransformer;
use App\Services\MedialImageableService;

/**
 * Class BaseService
 *
 * @package App\Services
 */
class BaseService
{
    /**
     * @var StorageService
     */
    protected $storageService;
    protected $medialImageableService;

    public function __construct()
    {
        $this->storageService = new StorageService();
        $this->medialImageableService = new MedialImageableService();
    }

    /**
     * Apply search
     *
     * @param  Builder  $builder
     * @param $term
     *
     * @return Builder
     */
    public function applySearch(Builder $builder, $term)
    {
        $builder->where(function ($query) use ($term) {
            foreach ($query->getModel()->getSearchable() as $searchColumn) {
                if (in_array($searchColumn, $query->getModel()->getEncrypted())) {
                    $query->orWhereEncrypted($searchColumn, '%' . $term . '%');
                } else {
                    $query->orWhere($searchColumn, 'LIKE', '%' . $term . '%');
                }
            }
        });

        return $builder;
    }

    /**
     * Apply filters
     *
     * @param  Builder|BaseModel  $builder
     * @param  array  $filters
     *
     * @return Builder
     */
    public function applyFilters(Builder $builder, array $filters)
    {
        foreach ($filters as $filter => $value) {
            if (in_array($filter, $builder->getModel()->getFilterable())) {
                if (in_array($filter, $builder->getModel()->getEncrypted())) {
                    $builder->whereEncrypted($filter, $value);
                } else {
                    $builder->where($filter, $value);
                }
            }
        }

        return $builder;
    }

    /**
     * Apply sort params.
     *
     * @param  Request  $request
     * @param  Builder|BaseModel  $builder
     *
     * @return Builder
     */
    public function applySortParams(Request $request, Builder $builder)
    {
        if ($request->has('sortColumn') || $request->has('sortOrder')) {
            $sortColumn = strtolower($request->get('sortColumn', 'id'));
            $sortOrder  = strtolower($request->get('sortOrder', 'asc'));

            if (in_array($sortColumn, $builder->getModel()->getSortable()) && in_array($sortOrder, ['asc', 'desc'])) {
                if (in_array($sortColumn, $builder->getModel()->getEncrypted())) {
                    return $builder->orderByEncrypted($sortColumn, $sortOrder);
                }

                return $builder->orderBy($sortColumn, $sortOrder);
            }
        }

        return $builder;
    }

    /**
     * Get pagination offset and limit.
     *
     * @param  Request  $request
     *
     * @return array
     */
    public function getPaginationParams(Request $request)
    {
        $limit = 10;
        if ($request->has('limit')) {
            $requestLimit = (int)$request->get('limit');

            if ($requestLimit > 0) {
                $limit = $requestLimit;
            }
        }

        $offset = 0;
        $page   = 1;

        if ($request->has('page')) {
            $requestPage = (int)$request->get('page');

            if ($requestPage > 1) {
                $page = $requestPage;
            }

            $offset = ($page - 1) * $limit;
        }

        return [
            'page'   => $page,
            'offset' => $offset,
            'limit'  => $limit
        ];
    }

    /**
     * Get pagination data.
     *
     * @param  Builder  $builder
     * @param $page
     * @param $limit
     *
     * @return array
     */
    public function getPaginationData(Builder $builder, $page, $limit)
    {
        $totalEntries = $builder->count();

        $totalPages = ceil($totalEntries / $limit);

        return [
            'currentPage'  => $page > $totalPages ? $totalPages : $page,
            'totalPages'   => $totalPages,
            'limit'        => $limit,
            'totalResult' => $totalEntries
        ];
    }

    public function transformerCategory(Category $category) {
        $manager = new Manager();
        $categoryInt = Category::query()->where('id', $category->id)->get();
        $formatCategory = new Collection($categoryInt, new CategoryTransformer());
        $formatCategory = $manager->createData($formatCategory)->toArray();
        $formatCategory = $formatCategory['data'][0];
        return $formatCategory;
    }

    public function transformerShowroom(Showroom $showroom) {
        $manager = new Manager();
        $showroomInt = Showroom::query()->where('id', $showroom->id)->get();
        $formatShowroom = new Collection($showroomInt, new ShowroomTransformer());
        $formatShowroom = $manager->createData($formatShowroom)->toArray();
        $formatShowroom = $formatShowroom['data'][0];
        return $formatShowroom;
    }
}
