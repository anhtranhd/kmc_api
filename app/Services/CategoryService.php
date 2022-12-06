<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Class CategoryService
 *
 * @package App\Services
 */
class CategoryService
{
    /**
     * Get category builder.
     *
     * @return Category|Builder
     */
    public function getCategoryBuilder()
    {

        $categories = Category::query();

        $categories = $categories->where('status', Category::ACTIVE)->whereNull('deleted_at');

        return $categories;
    }

    public function getAttributesByCategory($categoryId)
    {
        $category = $this->getCategoryBuilder()->where('id', $categoryId)->first();
        $category->load(['attributes' => function($q){
            return $q->where('status', Category::ACTIVE);
        }, 'attributes.attributeOptions']);
        return $category;
    }

    public function getCategoryAllTree($categoryId)
    {

        $categories = Category::query();

        $categories = $categories->where('status', Category::ACTIVE)->whereNull('deleted_at');
        if ($categoryId) {
            $categories = $categories->where(function ($subQuery) use ($categoryId) {
                $subQuery->where('id', $categoryId)->orWhere('parent_id', $categoryId);
            });
        }
        $categories = $categories->orderBy('parent_id');
        $categories = $categories->orderBy('order', 'desc')->get()->toArray();
        return $this->generateCategoryTree($categories);
    }
    private function generateCategoryTree(array $categories)
    {
        $new = [];
        foreach ($categories as $category) {
            unset($category['translations']);
            $new[$category['parent_id']][] = $category;
        }

        return $new ? $this->createTree($new, $new[0]) : [];
    }

    private function createTree(&$list, $parent)
    {
        $tree = array();
        foreach ($parent as $k => $l) {
            if (isset($list[$l['id']])) {
                $l['children'] = $this->createTree($list, $list[$l['id']]);
            }
            $tree[] = $l;
        }
        return $tree;
    }
}
