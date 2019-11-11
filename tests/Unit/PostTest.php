<?php

namespace Tests\Unit;

use App\Post;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function if_the_published_at_is_null_the_post_is_not_published()
    {
        $post = factory(Post::class)->create([
            'published_at' => null,
        ]);

        $posts = Post::published()->get();

        $this->assertFalse($posts->contains($post));
    }

    /** @test */
    public function if_the_published_at_is_in_the_future_the_post_is_not_published()
    {
        $post = factory(Post::class)->create([
            'published_at' => Carbon::tomorrow(),
        ]);

        $posts = Post::published()->get();

        $this->assertFalse($posts->contains($post));
    }

    /** @test */
    public function if_the_published_at_is_in_the_past_the_post_is_published()
    {
        $post = factory(Post::class)->create([
            'published_at' => Carbon::yesterday(),
        ]);

        $posts = Post::published()->get();

        $this->assertTrue($posts->contains($post));
    }

    /** @test */
    public function if_the_published_at_is_now_the_post_is_published()
    {
        $post = factory(Post::class)->create([
            'published_at' => Carbon::now(),
        ]);

        $posts = Post::published()->get();

        $this->assertTrue($posts->contains($post));
    }
}
