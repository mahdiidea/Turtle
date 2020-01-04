<?php

namespace Tests\Feature;

use App\Category;
use App\Content;
use App\Message;
use App\Post;
use App\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SeederTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');

        Artisan::call("migrate:fresh");
//        factory(Message::class, 5000)->create();

        factory(User::class, 40)->create();

//        factory(Category::class, 25)->create();

//        factory(Post::class, 150)->create();

//        $this->viewers();

//        $this->users_categories_role();

        $response->assertStatus(200);
    }

    private function users_categories_role()
    {
        $users = User::query()->inRandomOrder()->pluck("id");
        foreach ($users as $user) {
            $categories = Category::query()->inRandomOrder()->take(rand(1, 1000))->pluck("id");
            $inserts = collect();
            foreach ($categories as $category) {
                $inserts->push([
                    'user_id' => $user,
                    'category_id' => $category
                ]);
            }
            DB::table("users_categories_role")->insert($inserts->toArray());
        }
    }

    private function viewers()
    {
        $users = User::query()->inRandomOrder()->pluck("id");
        foreach ($users as $user) {
            $contents = Post::query()->inRandomOrder()->take(rand(1, 1000))->pluck("id");
            $inserts = collect();
            foreach ($contents as $content) {
                $inserts->push([
                    'user_id' => $user,
                    'post_id' => $content
                ]);
            }
            DB::table("posts_views")->insert($inserts->toArray());
        }
    }
}
