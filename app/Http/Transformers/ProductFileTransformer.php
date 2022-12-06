<?php namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;
use App\Image\Imagy;
use App\Models\ArtifactFile;

class ArtifactFileTransformer extends TransformerAbstract
{

  public function transform(ArtifactFile $item)
    {
        $data = [
            "id" => $item->id,
            "artifactId" => $item->artifact_id,
            "filename" => $item->filename,
            "path" => $item->path,
            "fileType" => $item->file_type,
            "isMain" => $item->is_main,
            "order" => $item->order,
        ];
        return $data;
    }

    private function getPath($path)
    {
        return asset($path);
    }
}
