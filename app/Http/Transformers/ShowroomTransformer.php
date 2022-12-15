<?php namespace App\Http\Transformers;

use App\Models\Artifact;
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
      $artifacts = Artifact::where('showroom_id', (int)$item->id)->orderby('sort')->get();
      $dataItem = [];
      foreach ($artifacts as $key => $artifact){
          $dataItem['item_'.$key+1 ] =  [
            'image_url' =>  $artifact->image_url ? $storageService->url($artifact->image_url) : "",
            'title_url' =>  $artifact->title,
            'description_url' =>  $artifact->description,
          ];
      }
      $video_url_1 = $item->video_type_1 == "type_upload" ? ($item->video_url_1 ? $storageService->url($item->video_url_1) : "") : $item->video_link_1;
      $video_url_2 = $item->video_type_2 == "type_upload" ? ($item->video_url_2 ? $storageService->url($item->video_url_2) : "") : $item->video_link_2;
      return array_merge($dataItem, [
          "id" => (int)$item->id,
          "name" => $item->name,
          "description" => $item->description,
          "status" => $item->status,
          "createdAt" => (string)$item->created_at,
          "updatedAt" => (string)$item->updated_at,
          'live_chat_url' => $item->live_chat_url,
          'poster_url' => $item->poster_url ? $storageService->url($item->poster_url) : "",
          'company_profile_url' => $item->company_profile_url,
          'web_url' => $item->web_url,
          'video_url_1'  =>$video_url_1,
          'video_url_2'  => $video_url_2,
          'logo_url_1' => $item->logo_url_1 ? $storageService->url($item->logo_url_1) : "",
          'ecommerce_url' => $item->ecommerce_url,
          'd_link' => $item->d_link,
          'd_title' => $item->d_title,
          'd_description' => $item->d_description,
      ]);
  }
}
