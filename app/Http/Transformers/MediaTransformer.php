<?php namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;
use App\Image\Imagy;

class MediaTransformer extends TransformerAbstract
{
  /**
   * Turn this item object into a generic array.
   *
   * @param $item
   * @return array
   */
  public function transform($collect)
    {
        $data = [
            "id" => $collect->id,
            "artifactId" => $collect->artifact_id,
            "filename" => $collect->filename,
            "path" => $collect->path,
            "fileType" => $collect->file_type,
            "isMain" => $collect->is_main,
            "order" => $collect->order,
        ];
        return $data;
    }

    private function getPath($path)
    {
        return asset($path);
    }
}
