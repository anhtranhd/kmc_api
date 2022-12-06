<?php namespace App\Http\Transformers;

use App\Models\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
  /**
   * Turn this item object into a generic array.
   *
   * @param $item
   * @return array
   */
  public function transform(Category $item)
  {
      return [
            "id" => (int)$item->id,
            "name" => $item->name,
            "description" => $item->description,
            "hasChild" => $item->hasChild,
            "link" => $item->link,
            "order" => $item->order,
            "status" => $item->status,
            "totalArtifact" => $item->totalArtifact,
            "image" => !empty($item->image) ? $item->image : '',
            "createdAt" => (string)$item->created_at,
            "updatedAt" => (string)$item->updated_at
      ];
  }
}
