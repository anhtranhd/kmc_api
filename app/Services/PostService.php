<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Class PostService
 *
 * @package App\Services
 */
class PostService extends BaseService
{
    /**
     * Get Post builder.
     *
     * @return Post|Builder
     */
    public function getPostBuilder()
    {

        $post = Post::query();

        $post = $post->where('status', Post::ACTIVE);

        return $post;
    }

    public function checkPost($postId)
    {

        $post = Post::query();

        $post = $post->where('status', Post::ACTIVE)->where('id', $postId)->first();

        return $post;
    }
}
