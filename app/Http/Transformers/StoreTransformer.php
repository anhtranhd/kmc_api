<?php namespace App\Http\Transformers;

use App\Models\Store;
use App\Models\artifact;
use League\Fractal\TransformerAbstract;

class StoreTransformer extends TransformerAbstract
{
  /**
   * Turn this item object into a generic array.
   *
   * @param $item
   * @return array
   */
  public function transform(Store $item)
  {
    $totalartifact = artifact::query()->where('store_id', $item->id)->count();
      return [
            "id" => (int)$item->id,
            "name" => $item->name,
            "type" => $item->type,
            "province" => $item->province,
            "city" => $item->city,
            "address" => $item->address,
            "description" => $item->description,
            "totalartifact" => $totalartifact,
            "createdAt" => (string)$item->created_at,
            "updatedAt" => (string)$item->updated_at
      ];
  }
}
