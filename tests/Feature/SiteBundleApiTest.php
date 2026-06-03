<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Support\SiteContentCache;
use Illuminate\Support\Collection;
use Database\Seeders\PortfolioContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SiteBundleApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PortfolioContentSeeder::class);
    }

    public function test_site_content_cache_normalizes_collection_lists(): void
    {
        $normalized = SiteContentCache::normalizePayload([
            'services' => collect([
                ['title' => 'A', 'image' => '/uploads/services/a.png', 'description' => 'x', 'icon' => 'Window'],
            ]),
        ]);

        $this->assertIsArray($normalized['services']);
        $this->assertSame('/uploads/services/a.png', $normalized['services'][0]['image']);
    }

    public function test_site_content_cache_detects_corrupt_legacy_entries(): void
    {
        $this->assertTrue(SiteContentCache::hasCorruptLists([
            'services' => (object) ['__PHP_Incomplete_Class_Name' => 'Illuminate\\Support\\Collection'],
        ]));
    }

    public function test_site_bundle_services_is_json_array_even_when_cached(): void
    {
        Service::query()->first()?->update([
            'image' => '/uploads/services/test-service.png',
        ]);

        Cache::flush();

        $this->getJson('/api/v1/site')
            ->assertOk()
            ->assertJsonPath('services.0.image', '/uploads/services/test-service.png');

        $cachedRaw = Cache::get(SiteContentCache::KEY);
        $this->assertIsString($cachedRaw);
        $cached = json_decode($cachedRaw, true);
        $this->assertIsArray($cached['services'] ?? null);

        $this->getJson('/api/v1/site')
            ->assertOk()
            ->assertJsonIsArray('services')
            ->assertJsonPath('services.0.image', '/uploads/services/test-service.png');
    }
}
