<?php

namespace Tests\Feature;

use App\Filament\Resources\CaseStudies\CaseStudyResource;
use App\Models\CaseStudy;
use App\Models\User;
use Database\Seeders\SystemPermissionSeeder;
use Database\Seeders\SystemRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CaseStudyEditorPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SystemPermissionSeeder::class);
        $this->seed(SystemRoleSeeder::class);
    }

    public function test_case_study_edit_page_uses_portfolio_editor_shell(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $caseStudy = CaseStudy::query()->create([
            'sort_order' => 0,
            'title' => 'Test study',
            'desc' => '<p>Description</p>',
            'impact' => '<p>Impact</p>',
            'stack' => ['Laravel'],
        ]);

        $this->actingAs($user)
            ->get(CaseStudyResource::getUrl('edit', ['record' => $caseStudy]))
            ->assertOk()
            ->assertSee('home-editor-page')
            ->assertSee('Edit case study')
            ->assertSee('home-editor-card--main')
            ->assertSee('Case study image')
            ->assertSee('fi-fo-rich-editor', false)
            ->assertSee('portfolio-stacked-editor-form')
            ->assertDontSee('home-editor-col--left')
            ->assertDontSee('home-editor-col--right');
    }

    public function test_case_study_edit_page_stacks_image_above_description(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $caseStudy = CaseStudy::query()->create([
            'sort_order' => 0,
            'title' => 'Stack order test',
            'desc' => '<p>Description</p>',
            'impact' => '<p>Impact</p>',
            'stack' => [],
        ]);

        $this->actingAs($user)
            ->get(CaseStudyResource::getUrl('edit', ['record' => $caseStudy]))
            ->assertOk()
            ->assertSeeInOrder([
                'Case study image',
                'Description',
                'Impact',
                'Technology stack',
            ], false);
    }
}
