<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PortfolioContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CmsResponseTimeTest extends TestCase
{
    use RefreshDatabase;

    private const MAX_SECONDS = 2.0;

    private const MAX_CACHED_SITE_SECONDS = 0.75;

    public function test_health_endpoint_responds_within_two_seconds(): void
    {
        $this->get('/up')->assertOk();

        $elapsed = $this->elapsedSeconds(fn () => $this->get('/up')->assertOk());

        $this->assertLessThan(self::MAX_SECONDS, $elapsed, "Health check took {$elapsed}s");
    }

    public function test_site_api_responds_within_two_seconds_and_caches(): void
    {
        $this->seed(PortfolioContentSeeder::class);
        Cache::flush();

        $elapsed = $this->elapsedSeconds(fn () => $this->getJson('/api/v1/site'));

        $this->assertLessThan(self::MAX_SECONDS, $elapsed, "Site API (cold) took {$elapsed}s");

        $cachedElapsed = $this->elapsedSeconds(fn () => $this->getJson('/api/v1/site')->assertOk());

        $this->assertLessThan(
            self::MAX_CACHED_SITE_SECONDS,
            $cachedElapsed,
            "Site API (cached) took {$cachedElapsed}s",
        );
    }

    public function test_notification_summary_responds_within_two_seconds(): void
    {
        $user = User::factory()->create();

        $elapsed = $this->elapsedSeconds(
            fn () => $this->actingAs($user)
                ->getJson('/admin/api/workspace/notifications/summary')
                ->assertOk()
                ->assertJsonStructure(['unread_count']),
        );

        $this->assertLessThan(self::MAX_SECONDS, $elapsed, "Notification summary took {$elapsed}s");
    }

  /**
     * @param  callable(): mixed  $request
     */
    private function elapsedSeconds(callable $request): float
    {
        $started = microtime(true);
        $request();

        return microtime(true) - $started;
    }
}
