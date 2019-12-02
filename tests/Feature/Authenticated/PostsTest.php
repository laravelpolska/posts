<?php

namespace Tests\Feature\Authenticated;

use App\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostsTest extends AuthenticatedTestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_post_can_be_created()
    {
        $this->post('/posts', [
            'published_at' => '2019-11-19 12:00:00',
            'title' => 'Odebrał żelazko zamiast telefonu',
            'body' => 'Miał pomóc żonie, a skończyło się tragedią.',
        ]);

        $this->assertDatabaseHas('posts', [
            'user_id' => $this->user->id,
            'published_at' => '2019-11-19 12:00:00',
            'title' => 'Odebrał żelazko zamiast telefonu',
            'body' => 'Miał pomóc żonie, a skończyło się tragedią.',
        ]);
    }

    /** @test */
    public function the_title_field_is_required()
    {
        $response = $this->post('/posts', [
            'title' => null,
        ]);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function the_title_field_must_be_unique()
    {
        $existingPost = factory(Post::class)->create([
            'title' => 'Wrabiał krowę w morderstwo cioci',
        ]);

        $response = $this->post('/posts', [
            'title' => 'Wrabiał krowę w morderstwo cioci',
        ]);

        $response->assertSessionHasErrors('title');
    }
}
