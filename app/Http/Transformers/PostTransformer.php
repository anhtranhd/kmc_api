<?php namespace App\Http\Transformers;

use App\Models\Post;
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract
{
  /**
   * Turn this item object into a generic array.
   *
   * @param $item
   * @return array
   */
  public function transform(Post $item)
  {
    return [
      "id" => (int)$item->id,
      "short_title" => $item->short_title,
      "title" => $item->title,
      "avatar" => $item->avatar,
      "source" => $item->source,
      "type" => $item->type,
      "shortDesc" => $item->short_desc,
      "description" => $item->description,
      "images" => $this->getMediaFileTranformerData($item->images),
      "order" => $item->order,
      "status" => $item->status,
      "postDate" => (string)$item->post_date,
      "createdAt" => (string)$item->created_at,
      "updatedAt" => (string)$item->updated_at
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
