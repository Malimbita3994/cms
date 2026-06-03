<?php

namespace App\Support;

use App\Filament\Pages\About;
use App\Filament\Pages\ChangePassword;
use App\Filament\Pages\Contact;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\Home;
use App\Filament\Resources\CaseStudies\CaseStudyResource;
use App\Filament\Resources\Insights\InsightResource;
use App\Filament\Resources\Permissions\PermissionResource;
use App\Filament\Resources\PortfolioProjects\PortfolioProjectResource;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Resources\Skills\SkillResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\CaseStudy;
use App\Models\ContactMessage;
use App\Models\Insight;
use App\Models\PortfolioProject;
use App\Models\Service;
use App\Models\Skill;
use App\Models\User;
use App\Support\ContactMessageSanitizer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class AdminGlobalSearch
{
    /** @var list<array{label: string, keywords: list<string>, title: string, subtitle: string, url: string, icon: string, group: string}>|null */
    private static ?array $navigationIndex = null;

    public static function clearNavigationCache(): void
    {
        self::$navigationIndex = null;
    }

    /**
     * @return array{query: string, groups: array<int, array{label: string, items: array<int, array<string, mixed>>}>, took_ms: int}
     */
    public function search(string $query, int $limitPerGroup = 8): array
    {
        $started = microtime(true);
        $query = trim($query);

        if ($query === '') {
            return [
                'query' => '',
                'groups' => $this->defaultGroups(),
                'took_ms' => (int) round((microtime(true) - $started) * 1000),
            ];
        }

        $tokens = $this->tokenize($query);
        $scored = collect();

        foreach ($this->navigationIndex() as $item) {
            $score = $this->scoreItem($item['title'], $item['subtitle'], $item['keywords'], $tokens, $query);

            if ($score > 0) {
                $scored->push([...$item, 'score' => $score]);
            }
        }

        foreach ($this->contentProviders() as $provider) {
            foreach ($provider() as $item) {
                $score = $this->scoreItem($item['title'], $item['subtitle'], $item['keywords'], $tokens, $query);

                if ($score > 0) {
                    $scored->push([...$item, 'score' => $score]);
                }
            }
        }

        $groups = $scored
            ->sortByDesc('score')
            ->groupBy('group')
            ->map(function (Collection $items, string $label) use ($limitPerGroup): array {
                return [
                    'label' => $label,
                    'items' => $items
                        ->take($limitPerGroup)
                        ->map(fn (array $item): array => [
                            'id' => $item['id'] ?? Str::slug($item['title'].'-'.$item['url']),
                            'title' => $item['title'],
                            'subtitle' => $item['subtitle'],
                            'url' => $item['url'],
                            'icon' => $item['icon'],
                            'badge' => $item['badge'] ?? null,
                            'score' => $item['score'],
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->sortBy(fn (array $group): int => $this->groupOrder($group['label']))
            ->values()
            ->all();

        return [
            'query' => $query,
            'groups' => $groups,
            'took_ms' => (int) round((microtime(true) - $started) * 1000),
        ];
    }

    /**
     * @return array<int, array{label: string, items: array<int, array<string, mixed>>}>
     */
    protected function defaultGroups(): array
    {
        $quick = collect($this->navigationIndex())
            ->take(8)
            ->map(fn (array $item): array => [
                'id' => Str::slug($item['title'].'-'.$item['url']),
                'title' => $item['title'],
                'subtitle' => $item['subtitle'],
                'url' => $item['url'],
                'icon' => $item['icon'],
                'badge' => 'Quick',
            ])
            ->values()
            ->all();

        return [
            [
                'label' => 'Quick navigation',
                'items' => $quick,
            ],
        ];
    }

    /**
     * @return list<string>
     */
    protected function tokenize(string $query): array
    {
        return collect(preg_split('/\s+/u', mb_strtolower($query)) ?: [])
            ->filter(fn (string $token): bool => mb_strlen($token) >= 1)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  list<string>  $keywords
     * @param  list<string>  $tokens
     */
    protected function scoreItem(string $title, string $subtitle, array $keywords, array $tokens, string $rawQuery): int
    {
        $haystack = mb_strtolower(implode(' ', [$title, $subtitle, ...$keywords]));
        $titleLower = mb_strtolower($title);
        $queryLower = mb_strtolower(trim($rawQuery));
        $score = 0;

        if ($titleLower === $queryLower) {
            $score += 120;
        } elseif (str_starts_with($titleLower, $queryLower)) {
            $score += 75;
        } elseif (str_contains($titleLower, $queryLower)) {
            $score += 45;
        }

        if (str_contains($haystack, $queryLower)) {
            $score += 25;
        }

        if ($tokens !== []) {
            foreach ($tokens as $token) {
                if (str_contains($titleLower, $token)) {
                    $score += 22;
                } elseif (str_contains($haystack, $token)) {
                    $score += 12;
                } else {
                    return 0;
                }
            }
        }

        similar_text($titleLower, $queryLower, $percent);

        if ($percent >= 55) {
            $score += (int) round($percent / 4);
        }

        return $score;
    }

    /**
     * @return list<array{label: string, keywords: list<string>, title: string, subtitle: string, url: string, icon: string, group: string, id?: string, badge?: string}>
     */
    protected function navigationIndex(): array
    {
        if (self::$navigationIndex !== null) {
            return self::$navigationIndex;
        }

        $items = [
            $this->nav('Dashboard', 'Overview and analytics', Dashboard::getUrl(), 'chart', 'Pages', ['dashboard', 'home', 'analytics']),
            $this->nav('Home page', 'Edit landing content', Home::getUrl(), 'home', 'Pages', ['home', 'landing', 'hero']),
            $this->nav('About', 'Profile and bio', About::getUrl(), 'user', 'Pages', ['about', 'profile', 'bio']),
            $this->nav('Skills', 'Skill blocks and levels', SkillResource::getUrl('index'), 'academic', 'Pages', ['skills', 'stack']),
            $this->nav('Projects', 'Portfolio projects', PortfolioProjectResource::getUrl('index'), 'folder', 'Pages', ['projects', 'portfolio', 'work']),
            $this->nav('Case studies', 'Home page project case studies', CaseStudyResource::getUrl('index'), 'document', 'Pages', ['case', 'studies', 'projects', 'home']),
            $this->nav('Services', 'Service offerings', ServiceResource::getUrl('index'), 'cube', 'Pages', ['services', 'offerings']),
            $this->nav('Insights', 'Articles and posts', InsightResource::getUrl('index'), 'document', 'Pages', ['insights', 'blog', 'articles']),
            $this->nav('Contact', 'Contact details and inbox', Contact::getUrl(), 'mail', 'Pages', ['contact', 'email', 'inbox', 'messages']),
            $this->nav('Change password', 'Security settings', ChangePassword::getUrl(), 'key', 'Account', ['password', 'security']),
        ];

        if (FilamentPermissions::canManageAccessControl()) {
            $items[] = $this->nav('Users', 'Admin accounts', UserResource::getUrl('index'), 'users', 'Admin', ['users', 'accounts', 'team']);
            $items[] = $this->nav('Roles', 'Permission groups', RoleResource::getUrl('index'), 'shield', 'Admin', ['roles', 'permissions']);
            $items[] = $this->nav('Permissions', 'Access control list', PermissionResource::getUrl('index'), 'lock', 'Admin', ['permissions', 'acl']);
        }

        $items[] = $this->nav('New project', 'Create portfolio project', PortfolioProjectResource::getUrl('create'), 'plus', 'Actions', ['create', 'add', 'project']);
        $items[] = $this->nav('New case study', 'Create case study', CaseStudyResource::getUrl('create'), 'plus', 'Actions', ['create', 'add', 'case', 'study']);
        $items[] = $this->nav('New service', 'Create service', ServiceResource::getUrl('create'), 'plus', 'Actions', ['create', 'add', 'service']);
        $items[] = $this->nav('New insight', 'Create insight', InsightResource::getUrl('create'), 'plus', 'Actions', ['create', 'add', 'insight']);

        return self::$navigationIndex = $items;
    }

    /**
     * @return list<callable(): list<array<string, mixed>>>
     */
    protected function contentProviders(): array
    {
        return [
            fn (): array => $this->searchProjects(),
            fn (): array => $this->searchServices(),
            fn (): array => $this->searchInsights(),
            fn (): array => $this->searchSkills(),
            fn (): array => $this->searchCaseStudies(),
            fn (): array => $this->searchUsers(),
            fn (): array => $this->searchContactMessages(),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function searchProjects(): array
    {
        return PortfolioProject::query()
            ->orderByDesc('updated_at')
            ->limit(40)
            ->get(['id', 'title', 'description', 'technologies', 'updated_at'])
            ->map(function (PortfolioProject $project): array {
                $tech = is_array($project->technologies) ? implode(' ', $project->technologies) : '';

                return [
                    'id' => 'project-'.$project->id,
                    'title' => $project->title,
                    'subtitle' => Str::limit($project->description ?? $tech, 80),
                    'url' => PortfolioProjectResource::getUrl('edit', ['record' => $project]),
                    'icon' => 'folder',
                    'group' => 'Projects',
                    'keywords' => array_filter([$tech, $project->description]),
                    'badge' => 'Project',
                ];
            })
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function searchServices(): array
    {
        return Service::query()
            ->orderByDesc('updated_at')
            ->limit(40)
            ->get(['id', 'title', 'description', 'updated_at'])
            ->map(fn (Service $service): array => [
                'id' => 'service-'.$service->id,
                'title' => $service->title,
                'subtitle' => Str::limit((string) $service->description, 80),
                'url' => ServiceResource::getUrl('edit', ['record' => $service]),
                'icon' => 'cube',
                'group' => 'Services',
                'keywords' => [(string) $service->description],
                'badge' => 'Service',
            ])
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function searchInsights(): array
    {
        return Insight::query()
            ->orderByDesc('updated_at')
            ->limit(40)
            ->get(['id', 'title', 'excerpt', 'updated_at'])
            ->map(fn (Insight $insight): array => [
                'id' => 'insight-'.$insight->id,
                'title' => $insight->title,
                'subtitle' => Str::limit((string) $insight->excerpt, 80),
                'url' => InsightResource::getUrl('edit', ['record' => $insight]),
                'icon' => 'document',
                'group' => 'Insights',
                'keywords' => [(string) $insight->excerpt],
                'badge' => 'Insight',
            ])
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function searchSkills(): array
    {
        return Skill::query()
            ->orderByDesc('updated_at')
            ->limit(40)
            ->get(['id', 'name', 'focus', 'level', 'updated_at'])
            ->map(fn (Skill $skill): array => [
                'id' => 'skill-'.$skill->id,
                'title' => $skill->name,
                'subtitle' => trim(($skill->focus ?? '').' · Level '.($skill->level ?? '—')),
                'url' => SkillResource::getUrl('edit', ['record' => $skill]),
                'icon' => 'academic',
                'group' => 'Skills',
                'keywords' => [(string) $skill->focus],
                'badge' => 'Skill',
            ])
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function searchCaseStudies(): array
    {
        return CaseStudy::query()
            ->orderByDesc('updated_at')
            ->limit(30)
            ->get(['id', 'title', 'desc', 'impact', 'updated_at'])
            ->map(fn (CaseStudy $study): array => [
                'id' => 'case-'.$study->id,
                'title' => $study->title,
                'subtitle' => Str::limit((string) ($study->desc ?? $study->impact), 80),
                'url' => CaseStudyResource::getUrl('edit', ['record' => $study]),
                'icon' => 'briefcase',
                'group' => 'Case studies',
                'keywords' => [(string) $study->desc, (string) $study->impact],
                'badge' => 'Case study',
            ])
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function searchContactMessages(): array
    {
        if (! Schema::hasTable('contact_messages')) {
            return [];
        }

        return ContactMessage::query()
            ->orderByDesc('created_at')
            ->limit(30)
            ->get(['id', 'name', 'email', 'message', 'created_at'])
            ->map(fn (ContactMessage $message): array => [
                'id' => 'contact-message-'.$message->id,
                'title' => $message->name,
                'subtitle' => Str::limit($message->email.' — '.ContactMessageSanitizer::plainText((string) $message->message), 80),
                'url' => Contact::getUrl(),
                'icon' => 'mail',
                'group' => 'Inbox',
                'keywords' => [$message->email, ContactMessageSanitizer::plainText((string) $message->message)],
                'badge' => 'Message',
            ])
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function searchUsers(): array
    {
        if (! FilamentPermissions::canManageAccessControl()) {
            return [];
        }

        return User::query()
            ->orderByDesc('updated_at')
            ->limit(25)
            ->get(['id', 'name', 'email', 'username', 'updated_at'])
            ->map(fn (User $user): array => [
                'id' => 'user-'.$user->id,
                'title' => $user->name,
                'subtitle' => $user->email.($user->username ? ' · @'.$user->username : ''),
                'url' => UserResource::getUrl('edit', ['record' => $user]),
                'icon' => 'users',
                'group' => 'Users',
                'keywords' => array_filter([$user->email, $user->username]),
                'badge' => 'User',
            ])
            ->all();
    }

    /**
     * @param  list<string>  $keywords
     * @return array{label: string, keywords: list<string>, title: string, subtitle: string, url: string, icon: string, group: string}
     */
    protected function nav(string $title, string $subtitle, string $url, string $icon, string $group, array $keywords): array
    {
        return [
            'title' => $title,
            'subtitle' => $subtitle,
            'url' => $url,
            'icon' => $icon,
            'group' => $group,
            'keywords' => $keywords,
        ];
    }

    protected function groupOrder(string $label): int
    {
        return match ($label) {
            'Pages' => 0,
            'Actions' => 1,
            'Projects' => 2,
            'Services' => 3,
            'Insights' => 4,
            'Skills' => 5,
            'Case studies' => 6,
            'Inbox' => 7,
            'Users' => 8,
            'Admin' => 9,
            default => 50,
        };
    }
}
