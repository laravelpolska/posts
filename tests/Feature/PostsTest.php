<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_posts_show_route_can_be_accessed()
    {
        // Arrange
        // Dodajemy do bazy danych wpis
        $post = factory(Post::class)->create([
            'title' => 'Wrabiał krowę w morderstwo cioci',
        ]);

        // Act
        // Wykonujemy zapytanie pod adres wpisu
        $response = $this->get('/posts/' . $post->id);

        // Assert
        // Sprawdzamy że w odpowiedzi znajduje się tytuł wpisu
        $response->assertStatus(200)
            ->assertSeeText('Wrabiał krowę w morderstwo cioci');
    }

    /** @test */
    public function the_body_attribute_is_shown_on_the_posts_show_view()
    {
        $post = factory(Post::class)->create([
            'body' => 'Mroczna tajemnica mordu w oborze długo spędzała sen z oczu policjantom z Lublina. Kto zabił 88-letnią kobietę i jej krowę?',
        ]);

        $response = $this->get('/posts/' . $post->id);

        $response->assertSeeText('Mroczna tajemnica mordu w oborze długo spędzała sen z oczu policjantom z Lublina. Kto zabił 88-letnią kobietę i jej krowę?');
    }

    /** @test */
    public function only_published_posts_are_shown_on_the_posts_index_view()
    {
        $publishedPost = factory(Post::class)->create([
            'published_at' => Carbon::yesterday(),
        ]);

        $unpublishedPost = factory(Post::class)->create([
            'published_at' => Carbon::tomorrow(),
        ]);

        $response = $this->get('/posts');

        $response->assertStatus(200)
            ->assertSeeText($publishedPost->title)
            ->assertDontSeeText($unpublishedPost->title);
    }

    /** @test */
    public function a_post_can_be_created()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)->post('/posts', [
            'published_at' => '2019-11-19 12:00:00',
            'title' => 'Odebrał żelazko zamiast telefonu',
            'body' => 'Miał pomóc żonie, a skończyło się tragedią.',
        ]);

        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'published_at' => '2019-11-19 12:00:00',
            'title' => 'Odebrał żelazko zamiast telefonu',
            'body' => 'Miał pomóc żonie, a skończyło się tragedią.',
        ]);
    }

    /** @test */
    public function guests_cannot_create_posts()
    {
        $response = $this->post('/posts', []);

        $response->assertRedirect('/login');
    }
}
