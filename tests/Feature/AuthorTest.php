<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Author;
use PHPUnit\Framework\Attributes\Test;

class AuthorTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_an_author()
    {
        $response = $this->postJson('/api/authors', [
            'name' => 'John Doe',
            'bio' => 'A famous writer',
            'birth_date' => '1980-05-15'
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'John Doe']);

        $this->assertDatabaseHas('authors', ['name' => 'John Doe']);
    }

    #[Test]
    public function it_can_retrieve_authors()
    {
        $author = Author::factory()->create()

        $response = $this->getJson('/api/authors');

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => $author->name]);
    }

    #[Test]
    public function it_can_retrieve_a_single_author()
    {
        $author = Author::factory()->create();

        $response = $this->getJson("/api/authors/{$author->id}");

        $response->assertStatus(200)
                 ->assertJson(['id' => $author->id, 'name' => $author->name]);
    }

    #[Test]
    public function it_can_update_an_author()
    {
        $author = Author::factory()->create();

        $response = $this->putJson("/api/authors/{$author->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Updated Name']);

        $this->assertDatabaseHas('authors', ['name' => 'Updated Name']);
    }

    #[Test]
    public function it_can_delete_an_author()
    {
        $author = Author::factory()->create();

        $response = $this->deleteJson("/api/authors/{$author->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('authors', ['id' => $author->id]);
    }

    #[Test]
    public function it_returns_404_if_author_not_found()
    {
        $response = $this->getJson('/api/authors/999');

        $response->assertStatus(404);
    }

    #[Test]
    public function it_validates_author_creation()
    {
        $response = $this->postJson('/api/authors', [
            'name' => '',
            'birth_date' => 'invalid-date'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'birth_date']);
    }
}
