<?php namespace App\Http\Transformers;

use App\Models\Variant;
use League\Fractal\TransformerAbstract;

class VariantTransformer extends TransformerAbstract
{
  /**
   * Turn this item object into a generic array.
   *
   * @param $item
   * @return array
   */
  public function transform(Variant $item)
  {
      return [
            "id" => (int)$item->id,
            "sku" => $item->sku,
            "description" => $item->description,
            "price" => $item->price,
            "quantity" => $item->quantity,
            "status" => $item->status,
            "createdAt" => (string)$item->created_at,
            "updatedAt" => (string)$item->updated_at
      ];
  }
}
