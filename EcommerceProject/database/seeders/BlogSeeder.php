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
        /* Create 7 categories */
        $categories = Category::factory(15)->create();
        /* Create random 20 blogs with images */
        $blogs = Blog::factory(20)->has(
            Imageable::factory(1)->blog(),
            'thumbnail'
        )->create();

        /* Create random blog comments */
        foreach($blogs->keyBy('id') as $blogId => $blog) {
            /* Attach 2–5 random categories from existing categories */
            $blog->categories()->attach($categories->random(rand(2, 5))->pluck('id'));

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
