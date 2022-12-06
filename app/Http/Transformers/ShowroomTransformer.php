<?php namespace App\Http\Transformers;

use App\Models\Showroom;
use League\Fractal\TransformerAbstract;

class ShowroomTransformer extends TransformerAbstract
{
  /**
   * Turn this item object into a generic array.
   *
   * @param $item
   * @return array
   */
  public function transform(Showroom $item)
  {
      return [
            "id" => (int)$item->id,
            "name" => $item->name,
            "description" => $item->description,
            "image" => $item->image,
            "code" => $item->code,
            "totalArtifact" => $item->totalArtifact,
            "status" => $item->status,
            "createdAt" => (string)$item->created_at,
            "updatedAt" => (string)$item->updated_at
      ];
  }
}
