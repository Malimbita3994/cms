<?php

namespace App\Models;

use App\Models\Concerns\HasPublishing;
use Illuminate\Database\Eloquent\Model;

class HomePage extends Model
{
    use HasPublishing;

    protected $fillable = [
        'availability_prefix',
        'headline',
        'hero_summary',
        'hero_background_image',
        'hero_profile_image',
        'primary_cta_label',
        'primary_cta_url',
        'secondary_cta_label',
        'secondary_cta_url',
        'cv_cta_label',
        'projects_stat_title',
        'projects_stat_subtitle',
        'services_stat_title',
        'services_stat_subtitle',
        'insights_stat_title',
        'insights_stat_subtitle',
        'core_focus_title',
        'core_focus_blurb',
        'core_focus_strengths',
        'show_core_focus',
    ];

    protected function casts(): array
    {
        return [
            'core_focus_strengths' => 'array',
            'show_core_focus' => 'boolean',
        ];
    }
}
