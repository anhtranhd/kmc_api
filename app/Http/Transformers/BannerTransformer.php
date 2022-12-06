<?php namespace App\Http\Transformers;

use App\Models\Banner;
use League\Fractal\TransformerAbstract;

class BannerTransformer extends TransformerAbstract
{
  /**
   * Turn this item object into a generic array.
   *
   * @param $item
   * @return array
   */
  public function transform(Banner $item)
  {
      return [
            "id" => (int)$item->id,
            "name" => $item->name,
            "order" => $item->order,
            "status" => $item->status,
            "image" => !empty($item->image) ? $item->image : '',
            "createdAt" => (string)$item->created_at,
            "updatedAt" => (string)$item->updated_at
      ];
  }
}
