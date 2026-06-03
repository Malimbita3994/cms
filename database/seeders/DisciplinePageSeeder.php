<?php

namespace Database\Seeders;

use App\Models\DisciplinePage;
use Illuminate\Database\Seeder;

class DisciplinePageSeeder extends Seeder
{
    public function run(): void
    {
        DisciplinePage::query()->updateOrCreate(
            ['slug' => DisciplinePage::SLUG_DUTIES],
            [
                'title' => 'Duties and Responsibilities',
                'hero_eyebrow' => 'Discipline',
                'hero_title' => 'System Analysis (Business Analysis) Duties',
                'hero_description' => 'Core duties aligned with institutional business analysis and e-Government delivery.',
                'items' => [
                    'To analyze the Institutional business models as well as their association with technology solutions;',
                    'To act as a bridge between business group with need or problem and the Technology teams offering a solution to a problem or need;',
                    'To drive and participates in design, development and implementation of enterprise wide applications;',
                    'To work closely with developers and testers to ensure business requirements are translated accurately into working technical designs;',
                    'To involve in development of new systems, business processes improvement, strategy planning or potentially organizational change;',
                    'To provide support in the implementation of e-Government initiatives throughout project life cycle.',
                    'To involve in solution testing and evaluation as providing quality assurance and control and communicating the deliverables state to the users.',
                    'To assist in the collection and consolidation projects required information and data;',
                    'To adhere to ICT project management standards defined by e-Government standard and Guidelines;',
                    'To perform any other official duties as may be assigned by immediate supervisor.',
                ],
                'body' => null,
                'is_published' => true,
            ],
        );

        DisciplinePage::query()->updateOrCreate(
            ['slug' => DisciplinePage::SLUG_RESPONSIBILITY],
            [
                'title' => 'Professional Responsibility',
                'hero_eyebrow' => 'Discipline',
                'hero_title' => 'Professional Responsibility',
                'hero_description' => 'Technical leadership, delivery management, and quality assurance expectations for Government ICT work.',
                'items' => null,
                'body' => '<p>Technical knowledge in determining end-to-end design requirements for projects involving line of business, software/hardware developers and vendors; strong skills in project planning, controlling and delivery management; capable of performing reviews and edits of requirements, specifications, business processes, feasibility studies, business cases and recommendations related to proposed solutions for Government ICT projects/requests; knowledgeable in Quality Assurance for developed ICT products/services; possession of an ICT-related certification (CISA/PMP/PRINCE2) is desirable.</p>',
                'is_published' => true,
            ],
        );
    }
}
