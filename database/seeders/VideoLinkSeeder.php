<?php

namespace Database\Seeders;

use App\Models\VideoLink;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VideoLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('video_links')->insert([
            [
                'title' => 'MAVRICK 440 | Me X Machine',
                'description' => null,
                'video_url' => 'https://www.youtube.com/watch?v=6Tzcu0xs_hU',
                'duration' => 30,
                'ads_type' => null,
                'category' => 'lifestyle',
                'country' => null,
                'source_platform' => 'YouTube',
                'views_count' => 4,
                'clicks_count' => 4,
                'cost_per_click' => 0.10,
                'status' => 'active',
                'created_at' => '2025-07-22 17:50:39',
                'updated_at' => '2025-07-25 06:33:18'
            ],
            [
                'title' => 'Pringles | Stuck In',
                'description' => null,
                'video_url' => 'https://www.youtube.com/watch?v=aP2up9N6H-g',
                'duration' => 30,
                'ads_type' => null,
                'category' => 'other',
                'country' => null,
                'source_platform' => 'YouTube',
                'views_count' => 1,
                'clicks_count' => 1,
                'cost_per_click' => 0.10,
                'status' => 'active',
                'created_at' => '2025-07-22 17:52:27',
                'updated_at' => '2025-07-29 05:53:29'
            ],
            [
                'title' => 'Pringles | Stuck In',
                'description' => null,
                'video_url' => 'https://www.youtube.com/watch?v=aP2up9N6H-g',
                'duration' => 30,
                'ads_type' => null,
                'category' => 'other',
                'country' => null,
                'source_platform' => 'YouTube',
                'views_count' => 1,
                'clicks_count' => 1,
                'cost_per_click' => 0.10,
                'status' => 'active',
                'created_at' => '2025-07-22 17:54:16',
                'updated_at' => '2025-07-22 23:59:22'
            ],
            [
                'title' => 'All the Best Moments are Better With Pepsi',
                'description' => null,
                'video_url' => 'https://www.youtube.com/watch?v=7oBZ8sBjdyQ',
                'duration' => 30,
                'ads_type' => null,
                'category' => 'other',
                'country' => null,
                'source_platform' => 'YouTube',
                'views_count' => 3,
                'clicks_count' => 3,
                'cost_per_click' => 0.10,
                'status' => 'active',
                'created_at' => '2025-07-22 17:55:36',
                'updated_at' => '2025-07-25 06:45:02'
            ],
            [
                'title' => 'SLING BABY | Doritos Commercial',
                'description' => null,
                'video_url' => 'https://www.youtube.com/watch?v=6vEEVNAOFFY',
                'duration' => 30,
                'ads_type' => null,
                'category' => 'other',
                'country' => null,
                'source_platform' => 'YouTube',
                'views_count' => 5,
                'clicks_count' => 5,
                'cost_per_click' => 0.10,
                'status' => 'active',
                'created_at' => '2025-07-22 17:56:54',
                'updated_at' => '2025-07-25 19:33:09'
            ],
            [
                'title' => 'LEKI Electric Motorbike Advert 2025',
                'description' => null,
                'video_url' => 'https://www.youtube.com/watch?v=NnwOO9aXt0E',
                'duration' => 30,
                'ads_type' => null,
                'category' => 'sports',
                'country' => null,
                'source_platform' => 'YouTube',
                'views_count' => 1,
                'clicks_count' => 1,
                'cost_per_click' => 0.10,
                'status' => 'active',
                'created_at' => '2025-07-22 18:04:14',
                'updated_at' => '2025-07-25 06:37:11'
            ],
            [
                'title' => 'Made For This | U.S. Marine Corps Commercial',
                'description' => null,
                'video_url' => 'https://www.youtube.com/watch?v=B4tJmlW6wWg',
                'duration' => 30,
                'ads_type' => null,
                'category' => 'lifestyle',
                'country' => null,
                'source_platform' => 'YouTube',
                'views_count' => 2,
                'clicks_count' => 2,
                'cost_per_click' => 0.10,
                'status' => 'active',
                'created_at' => '2025-07-22 18:05:20',
                'updated_at' => '2025-07-25 18:50:55'
            ],
            [
                'title' => 'U.S. Air Force: Future, Commercial',
                'description' => null,
                'video_url' => 'https://www.youtube.com/watch?v=tm4e86NVP1o',
                'duration' => 30,
                'ads_type' => null,
                'category' => 'lifestyle',
                'country' => null,
                'source_platform' => 'YouTube',
                'views_count' => 4,
                'clicks_count' => 4,
                'cost_per_click' => 0.10,
                'status' => 'active',
                'created_at' => '2025-07-22 18:06:40',
                'updated_at' => '2025-07-25 15:01:30'
            ],
            [
                'title' => 'Mitch Urban voiceover for Harley Davidson',
                'description' => null,
                'video_url' => 'https://www.youtube.com/watch?v=flq_rJ_t69k',
                'duration' => 13,
                'ads_type' => null,
                'category' => 'sports',
                'country' => null,
                'source_platform' => 'YouTube',
                'views_count' => 3,
                'clicks_count' => 3,
                'cost_per_click' => 0.10,
                'status' => 'active',
                'created_at' => '2025-07-22 18:08:08',
                'updated_at' => '2025-07-25 06:26:31'
            ],
            [
                'title' => 'Booking.com 2025 Big Game Ad',
                'description' => null,
                'video_url' => 'https://www.youtube.com/watch?v=laBjQ9uJDg8',
                'duration' => 30,
                'ads_type' => null,
                'category' => 'other',
                'country' => null,
                'source_platform' => 'YouTube',
                'views_count' => 4,
                'clicks_count' => 4,
                'cost_per_click' => 0.10,
                'status' => 'active',
                'created_at' => '2025-07-22 18:09:17',
                'updated_at' => '2025-07-25 14:52:00'
            ]
        ]);

        $this->command->info('Video links seeded successfully.');
    }
}
