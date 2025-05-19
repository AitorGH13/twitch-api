<?php // app/Repository/TopOfTheTopsRepository.php
namespace App\Repository;

use Illuminate\Support\Facades\DB;
use stdClass;

class TopOfTheTopsRepository
{
    public function getCacheMeta(): ?stdClass
    {
        return DB::table('topsofthetops')
            ->select('expires_at')
            ->limit(1)
            ->first();
    }

    public function clearCache(): void
    {
        DB::table('topsofthetops')->truncate();
    }

    public function insert(array $row, string $expiresAt): void
    {
        DB::table('topsofthetops')->insert([
            'game_id'      => $row['game_id'],
            'game_name'    => $row['game_name'],
            'user_name'    => $row['user_name'],
            'total_videos' => $row['total_videos'],
            'total_views'  => $row['total_views'],
            'mv_title'     => $row['most_viewed_title'],
            'mv_views'     => $row['most_viewed_views'],
            'mv_duration'  => $row['most_viewed_duration'],
            'mv_created_at'=> $row['most_viewed_created_at'],
            'expires_at'   => $expiresAt,
        ]);
    }

    public function all(): array
    {
        return DB::table('topsofthetops')
            ->select([
                'game_id','game_name','user_name','total_videos','total_views',
                'mv_title as most_viewed_title','mv_views as most_viewed_views',
                'mv_duration as most_viewed_duration','mv_created_at as most_viewed_created_at'
            ])->get()
            ->map(fn($r) => (array)$r)
            ->all();
    }
}
