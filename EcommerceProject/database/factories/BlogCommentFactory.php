<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogComment>
 */
class BlogCommentFactory extends Factory
{
    protected static ?array $userIds;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'blog_id' => Blog::factory(),
            'user_id' => $this->faker->randomElement($userIds ??= User::pluck('id')->toArray()),
            'content' => $this->faker->realTextBetween(30, 300),
            'parent_id' => null,
            'reply_to' => null,
        ];
    }

    public function users(Collection|array $userList): static
    {
        if($userList instanceof Collection) $userList = $userList->all();
        self::$userIds = array_map(fn($user) => $user instanceof User ? $user->id : $user, $userList);

        return $this;
    }

    public function reply(BlogComment $comment): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $comment->parent_id ?? $comment->id,
            'reply_to' => $comment->id,
        ]);
    }

    public function writeComment(string $content): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => $content,
        ]);
    }
}
