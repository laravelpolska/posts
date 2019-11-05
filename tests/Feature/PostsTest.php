<?php

namespace Tests\Feature;

use App\Post;
use Tests\TestCase;

class PostsTest extends TestCase
{
    /** @test */
    public function the_posts_show_route_can_be_accessed()
    {
        // Arrange
        // Dodajemy do bazy danych wpis
        $post = Post::create([
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
}
