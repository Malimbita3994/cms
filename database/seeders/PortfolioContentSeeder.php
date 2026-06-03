<?php

namespace Database\Seeders;

use App\Models\CareerTimelineEntry;
use App\Models\HomePage;
use App\Models\CaseStudy;
use App\Models\Insight;
use App\Models\PortfolioProject;
use App\Models\Profile;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class PortfolioContentSeeder extends Seeder
{
    public function run(): void
    {
        if (Profile::query()->exists()) {
            return;
        }

        SiteSetting::query()->create([
            'app_name' => 'System Analyst Portfolio',
            'site_title' => 'System Analyst Portfolio',
            'site_description' => 'Modern professional portfolio for a System Analyst specializing in architecture, integrations, and digital transformation.',
            'site_url' => 'https://example.com',
        ]);

        Profile::query()->create([
            'name' => 'Japhet Malimbita',
            'role' => 'System Analyst',
            'tagline' => 'Designing resilient digital ecosystems that connect people, processes, and platforms.',
            'summary' => 'System Analyst with strong experience in enterprise architecture, public-sector modernization, and end-to-end systems delivery. I translate policy and operational needs into scalable technical designs that improve service quality and decision-making.',
            'strengths' => [
                'System Architecture Design',
                'Software Development',
                'Integration (APIs, ESB, Government Systems)',
                'Data Management Systems',
            ],
            'email' => 'japhetfaustine39@gmail.com',
            'phone' => '+255 769 982 007',
            'location' => 'Dodoma, Tanzania',
            'linkedin_url' => 'https://www.linkedin.com',
            'github_url' => 'https://github.com',
            'image' => '/profile-hd.png?v=7',
            'about_eyebrow' => 'About Me',
            'about_heading_lead' => 'Driving Digital Transformation Through',
            'about_heading_accent' => 'Smart Systems',
            'about_strengths' => [
                ['title' => 'System Architecture', 'description' => 'Designing scalable, resilient enterprise systems.'],
                ['title' => 'API & ESB Integration', 'description' => 'Seamless integration across platforms and services.'],
                ['title' => 'Data Management', 'description' => 'Building reporting-ready and governed systems.'],
                ['title' => 'Digital Transformation', 'description' => 'Driving innovation in public sector systems.'],
            ],
            'about_approach_steps' => [
                '01 - Understand business needs deeply',
                '02 - Design scalable system architecture',
                '03 - Deliver secure and integrated systems',
                '04 - Continuously improve performance',
            ],
            'about_values' => [
                ['title' => 'Integrity', 'icon' => 'Shield'],
                ['title' => 'Innovation', 'icon' => 'Lightbulb'],
                ['title' => 'Excellence', 'icon' => 'Trophy'],
                ['title' => 'Impact', 'icon' => 'Target'],
                ['title' => 'Collaboration', 'icon' => 'Handshake'],
                ['title' => 'Accountability', 'icon' => 'ShieldCheck'],
                ['title' => 'Service', 'icon' => 'HeartHandshake'],
                ['title' => 'Growth Mindset', 'icon' => 'Rocket'],
            ],
            'about_page_hero_eyebrow' => 'About',
            'about_page_hero_title' => 'Professional Profile',
            'about_page_hero_description' => 'A deeper look at experience, strengths, values, and delivery philosophy.',
        ]);

        HomePage::query()->create([
            'availability_prefix' => 'Available for projects',
            'headline' => 'System Analyst',
            'hero_summary' => 'System Analyst with strong experience in enterprise architecture, public-sector modernization, and end-to-end systems delivery. I translate policy and operational needs into scalable technical designs that improve service quality and decision-making.',
            'hero_background_image' => '/Home.jpg?v=2',
            'hero_profile_image' => '/profile-hd.png?v=7',
            'primary_cta_label' => 'Start a Project',
            'primary_cta_url' => '/contact',
            'secondary_cta_label' => 'View my Works',
            'secondary_cta_url' => '#projects',
            'cv_cta_label' => 'View CV',
            'projects_stat_title' => 'Projects',
            'projects_stat_subtitle' => 'Delivered systems',
            'services_stat_title' => 'Services',
            'services_stat_subtitle' => 'Core offerings',
            'insights_stat_title' => 'Insights',
            'insights_stat_subtitle' => 'Published notes',
            'core_focus_title' => 'Core Focus',
            'core_focus_blurb' => 'Professional capability aligned with enterprise delivery outcomes.',
            'core_focus_strengths' => [
                [
                    'heading' => 'System Architecture Design',
                    'text' => 'Professional capability aligned with enterprise delivery outcomes.',
                ],
                [
                    'heading' => 'Software Development',
                    'text' => 'Professional capability aligned with enterprise delivery outcomes.',
                ],
                [
                    'heading' => 'Integration (APIs, ESB, Government Systems)',
                    'text' => 'Professional capability aligned with enterprise delivery outcomes.',
                ],
                [
                    'heading' => 'Data Management Systems',
                    'text' => 'Professional capability aligned with enterprise delivery outcomes.',
                ],
            ],
            'show_core_focus' => true,
        ]);

        $timeline = [
            ['2025 - Present', 'Senior System Analyst', 'Leading enterprise system architecture and digital transformation initiatives.'],
            ['2022 - 2025', 'System Developer', 'Built scalable government systems and API integrations.'],
            ['2019 - 2022', 'Software Engineer', 'Developed data-driven applications and reporting systems.'],
        ];
        foreach ($timeline as $i => [$period, $title, $desc]) {
            CareerTimelineEntry::query()->create([
                'sort_order' => $i,
                'period_label' => $period,
                'title' => $title,
                'description' => $desc,
            ]);
        }

        $skills = [
            ['Requirements Elicitation', 92, 'Workshops, BRDs, user stories, and acceptance criteria aligned to policy', 'ClipboardDocumentList'],
            ['Business Process Modeling', 90, 'AS-IS / TO-BE flows, BPMN, hand-offs, and operational pain-point analysis', 'RectangleStack'],
            ['Data Analysis & Reporting', 88, 'Source-to-report validation, KPI design, and decision-ready dashboards', 'ChartBarSquare'],
            ['System Architecture & Design', 93, 'Context views, interfaces, NFRs, and integration patterns for complex estates', 'Squares2X2'],
            ['SDLC & Change Governance', 86, 'Release readiness, UAT planning, risk logs, and controlled change windows', 'ArrowPath'],
        ];
        foreach ($skills as $i => [$name, $level, $focus, $icon]) {
            Skill::query()->create([
                'sort_order' => $i,
                'name' => $name,
                'level' => $level,
                'focus' => $focus,
                'icon' => $icon,
            ]);
        }

        $projects = [
            [
                'e-Elimu Portal',
                'A digital learning platform that centralizes curriculum resources, learner progress, and institutional reporting.',
                ['Laravel', 'MySQL', 'REST APIs', 'Docker'],
                ['Reduced manual reporting effort by over 40% through automated dashboards', 'Introduced role-based access control for teachers, students, and administrators'],
                '/projects/dashboard.svg',
                'Analytics-focused dashboard for educational workflow visibility.',
            ],
            [
                'TCMS (Teacher College Management System)',
                'Comprehensive management suite for admissions, academic records, and student lifecycle operations.',
                ['PHP', 'PostgreSQL', 'Bootstrap', 'Nginx'],
                ['Standardized data structures across departments for reliable analytics', 'Improved student service turnaround through integrated workflows'],
                '/projects/integration.svg',
                'Process automation architecture for end-to-end academic operations.',
            ],
            [
                'FDC-MIS',
                'Management information system for planning, monitoring, and evaluating facility-level development programs.',
                ['Laravel', 'Vue', 'MariaDB', 'API Gateway'],
                ['Established KPI-based monitoring modules aligned with institutional goals', 'Enabled multi-site reporting consolidation for executive decision support'],
                '/projects/analytics.svg',
                'Multi-site insight system for strategic planning and KPI tracking.',
            ],
            [
                'Contract Management System (CoMIS)',
                'End-to-end contract lifecycle platform from initiation to compliance tracking and renewal governance.',
                ['Laravel', 'Redis', 'PostgreSQL', 'Queue Workers'],
                ['Reduced compliance delays with automated reminders and escalation workflows', 'Implemented auditable approval trails for procurement transparency'],
                '/projects/contracts.svg',
                'Secure contract orchestration with audit-ready approval trails.',
            ],
        ];
        foreach ($projects as $i => [$title, $description, $technologies, $achievements, $image, $preview]) {
            PortfolioProject::query()->create([
                'sort_order' => $i,
                'title' => $title,
                'description' => $description,
                'technologies' => $technologies,
                'achievements' => $achievements,
                'image' => $image,
                'preview' => $preview,
            ]);
        }

        $services = [
            ['System Design & Architecture', 'Blueprinting scalable platforms with clear integration and governance models.', 'CommandLine'],
            ['Web Application Development', 'Building secure, maintainable business applications tailored to operational needs.', 'Window'],
            ['Digital Transformation Consulting', 'Guiding organizations through process digitization and technology adoption.', 'Sparkles'],
            ['Government System Integration', 'Connecting institutional systems with APIs, ESB layers, and regulatory platforms.', 'GlobeAlt'],
        ];
        foreach ($services as $i => [$title, $tagline, $icon]) {
            Service::query()->create([
                'sort_order' => $i,
                'title' => $title,
                'tagline' => $tagline,
                'description' => '<p>'.$tagline.'</p>',
                'icon' => $icon,
            ]);
        }

        $insights = [
            ['Designing Government-Ready APIs', 'Interoperable APIs require clear schemas, governance controls, and service-level agreements.', 'March 2026', '/insights/government-apis.svg'],
            ['From Legacy to Modular Platforms', 'How phased architecture migration lowers risk and preserves service continuity.', 'January 2026', '/insights/legacy-modular.svg'],
            ['Data Models That Support Policy Decisions', 'Building data structures that power transparent reporting and strategic planning.', 'November 2025', '/insights/data-policy.svg'],
        ];
        foreach ($insights as $i => [$title, $excerpt, $date, $image]) {
            Insight::query()->create([
                'sort_order' => $i,
                'title' => $title,
                'excerpt' => $excerpt,
                'display_date' => $date,
                'image' => $image,
            ]);
        }

        $caseStudies = [
            [
                'e-Elimu Portal',
                'Centralized education platform integrating scholarships, verification workflows, and graduation clearance operations.',
                'Improved nationwide service delivery and reduced processing time for students.',
                ['Laravel', 'PostgreSQL', 'REST API', 'Docker'],
            ],
            [
                'TCMS',
                'Teacher College Management System for admissions, finance, timetabling, and institutional reporting.',
                'Automated core academic and administrative workflows across institutions.',
                ['PHP', 'MySQL', 'Nginx', 'Bootstrap'],
            ],
            [
                'FDC-MIS',
                'Monitoring and evaluation system for planning, execution tracking, and KPI-based performance review.',
                'Enabled data-driven planning with executive-level reporting visibility.',
                ['Laravel', 'Vue', 'MariaDB', 'API Gateway'],
            ],
        ];
        foreach ($caseStudies as $i => [$title, $desc, $impact, $stack]) {
            CaseStudy::query()->create([
                'sort_order' => $i,
                'title' => $title,
                'desc' => $desc,
                'impact' => $impact,
                'stack' => $stack,
            ]);
        }
    }
}
