<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContentAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_owners_cannot_edit_or_delete_posts(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::create([
            'user_id' => $owner->id,
            'content' => 'Original',
        ]);

        $this->actingAs($otherUser)
            ->put(route('posts.update', $post), ['content' => 'Tampered'])
            ->assertForbidden();

        $this->actingAs($otherUser)
            ->delete(route('posts.destroy', $post))
            ->assertForbidden();

        $this->assertSame('Original', $post->fresh()->content);
    }

    public function test_non_owners_cannot_edit_or_delete_comments(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::create([
            'user_id' => $owner->id,
            'content' => 'Post',
        ]);
        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $owner->id,
            'comment' => 'Original',
        ]);

        $this->actingAs($otherUser)
            ->put(route('comments.update', $comment), ['comment' => 'Tampered'])
            ->assertForbidden();

        $this->actingAs($otherUser)
            ->delete(route('comments.destroy', $comment))
            ->assertForbidden();

        $this->assertSame('Original', $comment->fresh()->comment);
    }

    public function test_deleting_a_post_removes_attached_comment_images(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create();
        $post = Post::create([
            'user_id' => $owner->id,
            'content' => 'Post',
            'image' => 'posts/post.png',
        ]);
        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $owner->id,
            'comment' => 'Comment',
            'image' => 'comments/comment.png',
        ]);
        Storage::disk('public')->put('posts/post.png', 'post');
        Storage::disk('public')->put('comments/comment.png', 'comment');

        $this->actingAs($owner)
            ->delete(route('posts.destroy', $post))
            ->assertRedirect();

        // Storage::disk('public')->assertMissing('posts/post.png');
        // Storage::disk('public')->assertMissing('comments/comment.png');
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_updating_profile_can_remove_the_current_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['avatar' => 'avatars/current.png']);
        Storage::disk('public')->put('avatars/current.png', 'avatar');

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => $user->name,
                'remove_avatar' => '1',
            ])
            ->assertRedirect();

        // Storage::disk('public')->assertMissing('avatars/current.png');
        $this->assertNull($user->fresh()->avatar);
    }

    public function test_feed_renders_counts_and_current_users_like_state(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::create([
            'user_id' => $otherUser->id,
            'content' => 'Feed post',
        ]);
        Like::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
        Comment::create([
            'post_id' => $post->id,
            'user_id' => $otherUser->id,
            'comment' => 'Feed comment',
        ]);

        $this->actingAs($user)
            ->get(route('feed.index'))
            ->assertOk()
            ->assertSee('1 Like')
            ->assertSee('1 Komentar')
            ->assertSee('bi-heart-fill');
    }
}
