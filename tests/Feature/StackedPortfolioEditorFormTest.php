<?php

namespace Tests\Feature;

use App\Filament\Resources\CaseStudies\CaseStudyResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Resources\Skills\SkillResource;
use App\Models\CaseStudy;
use App\Models\Service;
use App\Models\Skill;
use App\Models\User;
use Database\Seeders\SystemPermissionSeeder;
use Database\Seeders\SystemRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StackedPortfolioEditorFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SystemPermissionSeeder::class);
        $this->seed(SystemRoleSeeder::class);
    }

    public function test_skill_create_page_stacks_image_above_description(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $this->actingAs($user)
            ->get(SkillResource::getUrl('create'))
            ->assertOk()
            ->assertSee('portfolio-stacked-editor-form')
            ->assertSeeInOrder(['Skill image', 'Description'], false)
            ->assertDontSee('home-editor-col--left');
    }

    public function test_skill_edit_page_stacks_image_above_description(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $skill = Skill::query()->create([
            'sort_order' => 0,
            'name' => 'Laravel',
            'level' => 90,
            'focus' => '<p>Backend</p>',
            'icon' => 'CommandLine',
        ]);

        $this->actingAs($user)
            ->get(SkillResource::getUrl('edit', ['record' => $skill]))
            ->assertOk()
            ->assertSee('portfolio-stacked-editor-form')
            ->assertSeeInOrder(['Skill image', 'Description'], false);
    }

    public function test_service_create_and_edit_pages_include_image_upload(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $this->actingAs($user)
            ->get(ServiceResource::getUrl('create'))
            ->assertOk()
            ->assertSee('Service image')
            ->assertSeeInOrder(['Service image', 'Description'], false);

        $service = Service::query()->create([
            'sort_order' => 0,
            'title' => 'Integration',
            'description' => '<p>API work</p>',
            'icon' => 'GlobeAlt',
        ]);

        $this->actingAs($user)
            ->get(ServiceResource::getUrl('edit', ['record' => $service]))
            ->assertOk()
            ->assertSee('Service image')
            ->assertSee('portfolio-stacked-editor-form');
    }

    public function test_case_study_create_page_uses_stacked_layout(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $this->actingAs($user)
            ->get(CaseStudyResource::getUrl('create'))
            ->assertOk()
            ->assertSee('portfolio-stacked-editor-form')
            ->assertSeeInOrder(['Case study image', 'Description', 'Impact'], false);
    }
}
