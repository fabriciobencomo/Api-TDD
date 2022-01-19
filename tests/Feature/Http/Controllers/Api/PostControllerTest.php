<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Models\Post;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_store()
    {
        $this->withoutExceptionHandling();
        $response = $this->json('POST', 'api/posts', [
            'title' => 'El post Mas Diverto'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => 'El post Mas Diverto'])
            ->assertStatus(201);

        $this->assertDatabaseHas('posts', ['title' => 'El post Mas Diverto']);
    }

    public function test_validate_title()
    {
        $response = $this->json('POST', 'api/posts', [
            'title' => ''
        ]);

        $response->assertStatus(422)
        ->assertJsonValidationErrors('title');
    }

    public function test_show()
    {
        $this->withoutExceptionHandling();
        $post = Post::factory()->create();
        $response = $this->json('GET',"/api/posts/$post->id");
        $response->assertJsonStructure(['id','title','created_at'])
        ->assertJson(['title' => $post->title])
        ->assertStatus(200);
    }

    public function test_404_show()
    {
        $response = $this->json('GET',"/api/posts/1000");
        $response->assertStatus(404);
    }

    public function test_update()
    {
        $post = Post::factory()->create();
        $this->withoutExceptionHandling();
        $response = $this->json('PUT', "api/posts/$post->id", [
            'title' => 'new'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => 'new'])
            ->assertStatus(200);

        $this->assertDatabaseHas('posts', ['title' => 'new']);
    }
}
