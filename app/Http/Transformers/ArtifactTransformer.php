<?php namespace App\Http\Transformers;

use App\Models\Artifact;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\CategoryTransformer;

class ArtifactTransformer extends TransformerAbstract
{
  /**
   * Turn this item object into a generic array.
   *
   * @param $item
   * @return array
   */
  public function transform($item)
  {
      return [
            "id" => (int)$item->id,
            "name" => $item->name,
            "sku" => $item->sku,
            "attributes" => $item->attributes,
            "category" => $item->category ? $item->category : '',
            "storeName" => $item->store_name,
            "brandName" => $item->brand_name,
            "description" => $item->description,
            "price" => $item->price,
            "quantity" => $item->quantity,
            "origin" => $item->origin,
            "totalComments" => $item->totalComments,
            "avgRating" => $item->avgRating,
            "avgStar" => $item->avgStar,
            "images" => $item->images,
            "videos" => $item->videos,
            "audios" => $item->audios,
            "arModel" => $item->arModel,
            "countStar" => $item->countStar,
            "countComment" => $item->countComment,
            "countMedia" => $item->countMedia,
            "status" => $item->status,
      ];
  }
  protected function getMediaFileTranformerData($files)
  {
      if (!empty($files)) {
          $data = (new MediaTransformer())->transform($files);

          return $data;
      } else {
          return null;
      }

  }
}
