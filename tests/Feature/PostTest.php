<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    use WithFaker;

    /**
     * @var array
     */
    protected $structure = [
        'id', 'title', 'content', 'html', 'description',
        'created_at', 'updated_at',
    ];

    /** @test */
    public function it_should_list_posts()
    {
        $response = $this->get('/post')
            ->assertJsonStructure( [
                'data' => [
                    '*' => $this->structure
                ],
            ]);
    }

    /** @test */
    public function it_should_get_post()
    {
        $post = factory(Post::class)->create();

        $this->get("/post/{$post->id}")
            ->assertSuccessful()
            ->assertSee($post->title);
    }

    /** @test */
    public function it_should_publish_post()
    {
        $post = factory(Post::class)->make();
        $user = factory(User::class)->create();

        // Unauthenticated
        $this->post('/post', [
                'title' => $post->title,
                'content' => $post->content,
            ])->assertRedirect('login');

        $this->assertGuest();

        // Authenticated
        $response = $this->actingAs($user)
            ->post('/post', [
                'title' => $post->title,
                'content' => $post->content,
            ])
            ->assertSuccessful()
            ->assertJsonStructure( [
                'data' => $this->structure
            ]);

        $this->assertAuthenticated()
            ->assertDatabaseHas('posts', [
                'title' => $post->title,
            ]);
    }

    /** @test */
    public function it_should_update_post()
    {
        $post = factory(Post::class)->create();
        $user = factory(User::class)->create();

        // Unauthenticated
        $this->patch("/post/{$post->id}", [
                'title' => $title = $this->faker->words(5, true),
                'content' => $post->content,
            ])->assertRedirect('login');

        $this->assertGuest();

        // Authenticated
        $response = $this->actingAs($user)
            ->patch("/post/{$post->id}", [
                'title' => $title,
                'content' => $post->content,
            ])
            ->assertSuccessful()
            ->assertJsonStructure( [
                'data' => $this->structure
            ]);

        $this->assertAuthenticated()
            ->assertDatabaseHas('posts', [
                'title' => $title,
            ]);
    }

    /** @test */
    public function it_should_delete_post()
    {
        $post = factory(Post::class)->create();
        $user = factory(User::class)->create();

        // Unauthenticated
        $this->delete("/post/{$post->id}")
            ->assertRedirect('login');

        $this->assertGuest();

        // Authenticated
        $this->actingAs($user)
            ->delete("/post/{$post->id}")
            ->assertSuccessful();

        $this->assertDatabaseMissing('posts', [
            'title' => $post->title,
        ]);
    }
}
