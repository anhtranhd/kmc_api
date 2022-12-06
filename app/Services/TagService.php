<?php


namespace App\Services;


use App\Models\Tagged;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TagService
{
    public function filter(string $tagNames)
    {
        if (!$tagNames) return;
        $slug = Str::slug($tagNames);
        return Tag::query()->whereLike(['slug'], $slug)->get();
    }

    public function addTags(string $tagNames, Model $model)
    {
        $tagNames = $this->makeTagArray($tagNames);
        foreach ($tagNames as $tagName) {
            $this->addSingleTag($model, $tagName);
        }
    }

    private function makeTagArray($tagNames)
    {
        if (is_array($tagNames) && count($tagNames) == 1) {
            $tagNames = reset($tagNames);
        }

        if (is_string($tagNames)) {
            $tagNames = explode(',', $tagNames);
        } elseif (!is_array($tagNames)) {
            $tagNames = array(null);
        }

        $tagNames = array_map('trim', $tagNames);

        return array_filter(array_unique(array_values($tagNames)));
    }

    private function addSingleTag($model, $tag)
    {
        if (!$tag) return;
        $slug = Str::slug($tag);
        $tagModel = Tag::query()->where('slug', 'LIKE ', '%' .$slug. '%')->first();

        if (!$tagModel) {
            $tagModel = Tag::create(['name' => $tag, 'slug' => $slug]);
        }

        $dataTagged = [
            'tag_id' => $tagModel->id,
            'taggable_type' => get_class($model),
            'taggable_id' => $model->id,
        ];
        $taggedModel = Tagged::where('tag_id', $tagModel->id)->where('taggable_type', get_class($model))->where('taggable_id', $model->id);
        if (!$taggedModel) {
            Tagged::create($dataTagged);
        }

    }

    /**
     * Replace the tags from this model
     *
     * @param string|array $tagNames
     * @param $model
     */
    public function retag($tagNames, $model)
    {
        $tagNames = $this->makeTagArray($tagNames);
        $currentTagNames = $this->tagNames($model);

        $deletions = array_diff($currentTagNames, $tagNames);
        $additions = array_diff($tagNames, $currentTagNames);

        $this->untag($model, $deletions);

        foreach ($additions as $tagName) {
            $this->addSingleTag($model, $tagName);
        }
    }

    /**
     * Return array of the tag names related to the current model
     *
     * @param Model $model
     * @return array
     */
    public function tagNames(Model $model): array
    {
        return $model->tagged->map(function ($item) {
                return $item->name;
            })->toArray() ?? [];
    }

    /**
     * Remove the tag from this model
     *
     * @param Model $model
     * @param string|array|null $tagNames (or null to remove all tags)
     */
    public function untag($model, $tagNames = null)
    {
        if (is_null($tagNames)) {
            $tagNames = $this->tagNames($model);
        }

        $tagNames = $this->makeTagArray($tagNames);

        foreach ($tagNames as $tagName) {
            $this->removeSingleTag($model, $tagName);
        }

    }

    private function removeSingleTag($model, $tagName)
    {
        $tagName = trim($tagName);

        $tagSlug = Str::slug($tagName);

        return $model->tagged()->where('slug', '=', $tagSlug)->delete();
    }


}
