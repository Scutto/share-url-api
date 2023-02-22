<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Url;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tags = \App\Models\Tag::factory(100)->create();
        for ($i = 0; $i < 10; $i++) {
            $users = \App\Models\User::factory(10000)->create();

            $users->chunk(1000)->each(
                function($chunk) use($tags, $users) {
                    $chunk->each(
                        function(User $user) use($tags, $users) {
                            $userUrls = \App\Models\Url::factory(rand(10, 15))
                                ->for($user)
                                ->create();
            
                            $userUrls->each(
                                function(Url $url) use($tags, $users) {
                                    $this->addTagsToUrl($url, $tags);
                                    $this->addLikesToUrl($url, $users);
                                }
                            );
            
                            $this->addFollowersToUser($user, $users);
                            var_dump($user->id);
                        }
                    );
                }
            );
        }
    }

    private function addLikesToUrl(Url $url, Collection $users): void {
        $randUsersIds = $users->random(rand(1, 4))->pluck('id');
        $temp = [];
        $randUsersIds->each(
            function($randUserId) use($url, &$temp) {
                $temp[] = ['url_id' => $url->id, 'user_id' => $randUserId];
            }
        );
        
        DB::table('urls_likes_pivot')->insert($temp);
    }

    private function addTagsToUrl(Url $url, Collection $tags): void {
        $randTagsIds = $tags->random(rand(1, 3))->pluck('id');
        $temp = [];
        $randTagsIds->each(
            function($randTagId) use($url, &$temp) {
                $temp[] = ['url_id' => $url->id, 'tag_id' => $randTagId];
            }
        );

        DB::table('urls_tags_pivot')->insert($temp);
    }

    private function addFollowersToUser(User $user, Collection $users): void {
        $randUsersIds = $users->random(rand(3, 10))->pluck('id');
        $temp = [];
        $randUsersIds->each(
            function($randUserId) use($user, &$temp) {
                $temp[] = ['follower_id' => $user->id, 'followed_id' => $randUserId];
            }
        );

        DB::table('followed_followers_pivot')->insert($temp);
    }
}
