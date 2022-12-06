<?php namespace App\Http\Transformers;

use App\Models\Showroom;
use App\Services\StorageService;
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
      $storageService = new StorageService();
      return [
            "id" => (int)$item->id,
            "name" => $item->name,
            "description" => $item->description,
            "code" => $item->code,
            "status" => $item->status,
            "createdAt" => (string)$item->created_at,
            "updatedAt" => (string)$item->updated_at,
            'chat' => $item->chat,
            'image_url_1' => $item->image_url_1 ? $storageService->url($item->image_url_1) : "",
            'image_url_2' => $item->image_url_2 ? $storageService->url($item->image_url_2) : "",
            'image_url_3' => $item->image_url_3 ? $storageService->url($item->image_url_3) : "",
            'video_url'  => $item->video_url ? $storageService->url($item->video_url) : "",
            'logo_url' => $item->logo_url ? $storageService->url($item->logo_url) : "",
            'shopee_url' => $item->shopee_url,
            '3d_link' => $item->d_link,
      ];
  }
}
