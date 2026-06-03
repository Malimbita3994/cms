<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CareerTimelineEntry;
use App\Models\DisciplinePage;
use App\Models\CaseStudy;
use App\Models\HomePage;
use App\Support\CoreFocusItems;
use App\Models\Insight;
use App\Models\PortfolioProject;
use App\Models\Profile;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Support\SiteContentCache;
use Illuminate\Support\Collection;

class PublicContentController extends Controller
{
    public function caseStudies()
    {
        $rows = CaseStudy::query()->published()->orderBy('sort_order')->orderBy('id')->get();

        return response()->json($rows->map(fn (CaseStudy $c) => [
            'title' => $c->title,
            'image' => $c->image ?? '',
            'desc' => $c->desc,
            'impact' => $c->impact,
            'stack' => $c->stack ?? [],
        ])->values()->all());
    }

    public function siteBundle()
    {
        $payload = SiteContentCache::remember(fn (): array => $this->buildSiteBundlePayload());

        if (SiteContentCache::hasCorruptLists($payload)) {
            SiteContentCache::flush();
            $payload = SiteContentCache::normalizePayload($this->buildSiteBundlePayload());
        }

        $payload = SiteContentCache::normalizePayload($payload);

        if (isset($payload['error'])) {
            return response()->json($payload, 503);
        }

        return response()
            ->json($payload)
            ->header('Cache-Control', 'public, max-age=60');
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildSiteBundlePayload(): array
    {
        $settings = SiteSetting::query()->first();
        $profile = Profile::query()->published()->first()
            ?? Profile::query()->first();

        if (! $settings || ! $profile) {
            return ['error' => 'CMS content not seeded'];
        }

        return [
            'appName' => $settings->app_name,
            'title' => $settings->site_title,
            'description' => $settings->site_description,
            'url' => $settings->site_url,
            'profile' => $this->profileArray($profile),
            'home' => $this->homeArray($profile),
            'careerTimeline' => $this->listArray(CareerTimelineEntry::query()
                ->published()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (CareerTimelineEntry $e) => [
                    'year' => $e->period_label,
                    'title' => $e->title,
                    'description' => $e->description,
                ])),
            'skills' => $this->listArray(Skill::query()->published()->orderBy('sort_order')->orderBy('id')->get()->map(fn (Skill $s) => [
                'name' => $s->name,
                'level' => $s->level,
                'focus' => $s->focus,
                'icon' => $s->icon,
                'image' => $s->image ?? '',
            ])),
            'projects' => $this->listArray(PortfolioProject::query()->published()->orderBy('sort_order')->orderBy('id')->get()->map(fn (PortfolioProject $p) => [
                'title' => $p->title,
                'description' => $p->description,
                'technologies' => $p->technologies ?? [],
                'achievements' => $p->achievements ?? [],
                'image' => $p->image,
                'preview' => $p->preview,
            ])),
            'services' => $this->listArray(Service::query()->published()->orderBy('sort_order')->orderBy('id')->get()->map(fn (Service $s) => [
                'title' => $s->title,
                'tagline' => $s->tagline ?? '',
                'image' => $s->image ?? '',
                'description' => $s->description,
                'icon' => $s->icon,
            ])),
            'insights' => $this->listArray(Insight::query()->published()->orderBy('sort_order')->orderBy('id')->get()->map(fn (Insight $i) => [
                'title' => $i->title,
                'excerpt' => $i->excerpt,
                'date' => $i->display_date,
                'image' => $i->image,
            ])),
            'caseStudies' => $this->listArray(CaseStudy::query()->published()->orderBy('sort_order')->orderBy('id')->get()->map(fn (CaseStudy $c) => [
                'title' => $c->title,
                'image' => $c->image ?? '',
                'desc' => $c->desc,
                'impact' => $c->impact,
                'stack' => $c->stack ?? [],
            ])),
            'discipline' => $this->disciplineArray(),
        ];
    }

    /**
     * @return array{duties: ?array<string, mixed>, professionalResponsibility: ?array<string, mixed>}
     */
    protected function disciplineArray(): array
    {
        $map = fn (?DisciplinePage $page): ?array => $page && ($page->is_published ?? true)
            ? [
                'slug' => $page->slug,
                'title' => $page->title,
                'hero' => [
                    'eyebrow' => $page->hero_eyebrow,
                    'title' => $page->hero_title,
                    'description' => $page->hero_description ?? '',
                ],
                'items' => $page->items ?? [],
                'body' => $page->body ?? '',
            ]
            : null;

        return [
            'duties' => $map(DisciplinePage::duties()),
            'professionalResponsibility' => $map(DisciplinePage::professionalResponsibility()),
        ];
    }

    /**
     * @param  Collection<int, mixed>  $items
     * @return array<int, mixed>
     */
    protected function listArray(Collection $items): array
    {
        return $items->values()->all();
    }

    /**
     * @return array<string, mixed>
     */
    protected function homeArray(Profile $profile): array
    {
        $home = HomePage::query()->published()->first();

        $defaultBlurb = 'Professional capability aligned with enterprise delivery outcomes.';
        $defaultStrengths = $profile->strengths ?? [];

        if (! $home) {
            return $this->defaultHomePayload($profile, $defaultStrengths, $defaultBlurb);
        }

        $strengths = CoreFocusItems::forApi(
            $home->core_focus_strengths ?? $defaultStrengths,
            $home->core_focus_blurb ?: $defaultBlurb,
        );

        return array_merge($this->defaultHomePayload($profile, $defaultStrengths, $defaultBlurb), [
            'heroBackgroundImage' => $home->hero_background_image,
            'headline' => $home->headline ?? $profile->role,
            'summary' => $home->hero_summary ?? $profile->summary,
            'heroProfileImage' => $home->hero_profile_image ?: ($profile->image ?? ''),
            'availabilityPrefix' => $home->availability_prefix ?? 'Available for projects',
            'primaryCta' => [
                'label' => $home->primary_cta_label ?? 'Start a Project',
                'url' => $home->primary_cta_url ?? '/contact',
            ],
            'secondaryCta' => [
                'label' => $home->secondary_cta_label ?? 'View my Works',
                'url' => $home->secondary_cta_url ?? '#projects',
            ],
            'cvCtaLabel' => $home->cv_cta_label ?? 'View CV',
            'stats' => [
                'projects' => [
                    'title' => $home->projects_stat_title ?? 'Projects',
                    'subtitle' => $home->projects_stat_subtitle ?? 'Delivered systems',
                ],
                'services' => [
                    'title' => $home->services_stat_title ?? 'Services',
                    'subtitle' => $home->services_stat_subtitle ?? 'Core offerings',
                ],
                'insights' => [
                    'title' => $home->insights_stat_title ?? 'Insights',
                    'subtitle' => $home->insights_stat_subtitle ?? 'Published notes',
                ],
            ],
            'coreFocus' => [
                'title' => $home->core_focus_title,
                'strengths' => $strengths,
                'blurb' => $home->core_focus_blurb ?: $defaultBlurb,
                'show' => $home->show_core_focus && count($strengths) > 0,
            ],
        ]);
    }

    /**
     * @param  array<int, string|array<string, mixed>>  $defaultStrengths
     * @return array<string, mixed>
     */
    protected function defaultHomePayload(Profile $profile, array $defaultStrengths, string $defaultBlurb): array
    {
        return [
            'heroBackgroundImage' => '/Home.jpg?v=2',
            'headline' => $profile->role,
            'summary' => $profile->summary,
            'heroProfileImage' => $profile->image ?? '',
            'availabilityPrefix' => 'Available for projects',
            'primaryCta' => ['label' => 'Start a Project', 'url' => '/contact'],
            'secondaryCta' => ['label' => 'View my Works', 'url' => '#projects'],
            'cvCtaLabel' => 'View CV',
            'stats' => [
                'projects' => ['title' => 'Projects', 'subtitle' => 'Delivered systems'],
                'services' => ['title' => 'Services', 'subtitle' => 'Core offerings'],
                'insights' => ['title' => 'Insights', 'subtitle' => 'Published notes'],
            ],
            'coreFocus' => [
                'title' => 'Core Focus',
                'strengths' => CoreFocusItems::forApi($defaultStrengths, $defaultBlurb),
                'blurb' => $defaultBlurb,
                'show' => count($defaultStrengths) > 0,
            ],
        ];
    }

    protected function profileArray(Profile $p): array
    {
        return [
            'name' => $p->name,
            'role' => $p->role,
            'tagline' => $p->tagline,
            'summary' => $p->summary,
            'strengths' => $p->strengths ?? [],
            'contact' => [
                'email' => $p->email,
                'phone' => $p->phone,
                'location' => $p->location,
                'linkedin' => $p->linkedin_url,
                'github' => $p->github_url,
            ],
            'image' => $p->image ?? '',
            'about' => $this->aboutArray($p),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function aboutArray(Profile $p): array
    {
        $defaultStrengths = [
            ['title' => 'System Architecture', 'description' => 'Designing scalable, resilient enterprise systems.'],
            ['title' => 'API & ESB Integration', 'description' => 'Seamless integration across platforms and services.'],
            ['title' => 'Data Management', 'description' => 'Building reporting-ready and governed systems.'],
            ['title' => 'Digital Transformation', 'description' => 'Driving innovation in public sector systems.'],
        ];

        return [
            'eyebrow' => $p->about_eyebrow ?? 'About Me',
            'headingLead' => $p->about_heading_lead ?? 'Driving Digital Transformation Through',
            'headingAccent' => $p->about_heading_accent ?? 'Smart Systems',
            'strengths' => $p->about_strengths ?? $defaultStrengths,
            'approach' => $p->about_approach_steps ?? [
                '01 - Understand business needs deeply',
                '02 - Design scalable system architecture',
                '03 - Deliver secure and integrated systems',
                '04 - Continuously improve performance',
            ],
            'values' => $p->about_values ?? [
                ['title' => 'Integrity', 'icon' => 'Shield'],
                ['title' => 'Innovation', 'icon' => 'Lightbulb'],
                ['title' => 'Excellence', 'icon' => 'Trophy'],
                ['title' => 'Impact', 'icon' => 'Target'],
                ['title' => 'Collaboration', 'icon' => 'Handshake'],
                ['title' => 'Accountability', 'icon' => 'ShieldCheck'],
                ['title' => 'Service', 'icon' => 'HeartHandshake'],
                ['title' => 'Growth Mindset', 'icon' => 'Rocket'],
            ],
            'pageHero' => [
                'eyebrow' => $p->about_page_hero_eyebrow ?? 'About',
                'title' => $p->about_page_hero_title ?? 'Professional Profile',
                'description' => $p->about_page_hero_description
                    ?? 'A deeper look at experience, strengths, values, and delivery philosophy.',
            ],
        ];
    }
}
