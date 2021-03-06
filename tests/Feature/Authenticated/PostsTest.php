<?php

namespace Tests\Feature\Authenticated;

use App\Post;
use App\User;
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

    /** @test */
    public function the_body_is_not_required()
    {
        $response = $this->post('/posts', [
            'body' => null,
        ]);

        $response->assertSessionDoesntHaveErrors('body');
    }

    /** @test */
    public function the_body_must_be_at_least_3_characters()
    {
        $response = $this->post('/posts', [
            'body' => 'aa',
        ]);

        $response->assertSessionHasErrors('body');
    }

    /** @test */
    public function the_published_at_is_not_required()
    {
        $response = $this->post('/posts', [
            'published_at' => null,
        ]);

        $response->assertSessionDoesntHaveErrors('published_at');
    }

    /** @test */
    public function the_published_at_must_be_a_valid_date()
    {
        $response = $this->post('/posts', [
            'published_at' => 'NOT-A-DATE-STRING',
        ]);

        $response->assertSessionHasErrors('published_at');
    }

    /** @test */
    public function a_user_can_update_their_own_posts()
    {
        $fred = factory(User::class)->create();
        $post = factory(Post::class)->create([
            'user_id' => $fred->id,
        ]);

        $this->actingAs($fred)->patch("/posts/{$post->id}" , [
            'published_at' => '2019-11-19 12:00:00',
            'title' => 'Odebrał żelazko zamiast telefonu',
            'body' => 'Miał pomóc żonie, a skończyło się tragedią.',
        ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'published_at' => '2019-11-19 12:00:00',
            'title' => 'Odebrał żelazko zamiast telefonu',
            'body' => 'Miał pomóc żonie, a skończyło się tragedią.',
        ]);
    }

    /** @test */
    public function a_user_cannot_update_other_users_posts()
    {
        $fred = factory(User::class)->create();
        $barney = factory(User::class)->create();
        $post = factory(Post::class)->create([
            'user_id' => $fred->id,
        ]);

        $response = $this->actingAs($barney)->patch("/posts/{$post->id}" , [
            'published_at' => '2019-11-19 12:00:00',
            'title' => 'Odebrał żelazko zamiast telefonu',
            'body' => 'Miał pomóc żonie, a skończyło się tragedią.',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function the_title_field_is_required_on_update()
    {
        $post = factory(Post::class)->create();

        $response = $this->patch("/posts/{$post->id}", [
            'title' => null,
        ]);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function the_title_field_must_be_unique_on_update()
    {
        $post = factory(Post::class)->create();
        $otherPost = factory(Post::class)->create([
            'title' => 'Wrabiał krowę w morderstwo cioci',
        ]);

        $response = $this->patch("/posts/{$post->id}", [
            'title' => 'Wrabiał krowę w morderstwo cioci',
        ]);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function the_current_title_is_ignored_for_the_unique_check_on_update()
    {
        $post = factory(Post::class)->create([
            'title' => 'Wrabiał krowę w morderstwo cioci',
        ]);

        $response = $this->patch("/posts/{$post->id}", [
            'title' => 'Wrabiał krowę w morderstwo cioci',
        ]);

        $response->assertSessionHasNoErrors('title');
    }

    /** @test */
    public function the_body_is_not_required_on_update()
    {
        $post = factory(Post::class)->create();

        $response = $this->patch("/posts/{$post->id}", [
            'body' => null,
        ]);

        $response->assertSessionDoesntHaveErrors('body');
    }

    /** @test */
    public function the_body_must_be_at_least_3_characters_on_update()
    {
        $post = factory(Post::class)->create();

        $response = $this->patch("/posts/{$post->id}", [
            'body' => 'aa',
        ]);

        $response->assertSessionHasErrors('body');
    }

    /** @test */
    public function the_published_at_is_not_required_on_update()
    {
        $post = factory(Post::class)->create();

        $response = $this->patch("/posts/{$post->id}", [
            'published_at' => null,
        ]);

        $response->assertSessionDoesntHaveErrors('published_at');
    }

    /** @test */
    public function the_published_at_must_be_a_valid_date_on_update()
    {
        $post = factory(Post::class)->create();

        $response = $this->patch("/posts/{$post->id}", [
            'published_at' => 'NOT-A-DATE-STRING',
        ]);

        $response->assertSessionHasErrors('published_at');
    }

    /** @test */
    public function admin_can_delete_posts()
    {
        $admin = factory(User::class)->create([
            'email' => 'admin@example.com',
        ]);
        $post = factory(Post::class)->create();

        $this->actingAs($admin)->delete("/posts/{$post->id}");

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    /** @test */
    public function non_admin_users_cannot_delete_posts()
    {
        $user = factory(User::class)->create([
            'email' => 'user@example.com',
        ]);
        $post = factory(Post::class)->create();

        $response = $this->actingAs($user)->delete("/posts/{$post->id}");

        $response->assertForbidden();
    }
}
