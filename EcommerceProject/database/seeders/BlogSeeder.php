<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\Category;
use App\Models\Imageable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* Create random 20 blogs with images and categories */
        $blogs = Blog::factory(20)->has(
            Imageable::factory(1)->blog(),
            'thumbnail'
        )->has(
            Category::factory(4),
            'categories'
        )->create();

        /* Create random blog comments */
        foreach($blogs->pluck('id') as $blogId) {
            /* Create random root comments */
            $blogComments = BlogComment::factory(rand(5, 40))->create([
                'blog_id' => $blogId
            ]);

            /* Create random sub comments */
            foreach($blogComments->pluck('id') as $blogCommentId) {
                $subComments = BlogComment::factory(rand(0, 5))->create([
                    'blog_id' => $blogId,
                    'parent_id' => $blogCommentId,
                    'reply_to' => $blogCommentId
                ]);

                /* Create random sub sub comments */
                foreach($subComments->pluck('id') as $subCommentId) {
                    BlogComment::factory(rand(0, 3))->create([
                        'blog_id' => $blogId,
                        'parent_id' => $blogCommentId,
                        'reply_to' => $subCommentId
                    ]);
                }
            }
        }
    }
}
