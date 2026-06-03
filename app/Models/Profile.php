<?php

namespace App\Models;

use App\Models\Concerns\HasPublishing;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasPublishing;

    protected $fillable = [
        'name',
        'role',
        'tagline',
        'summary',
        'strengths',
        'email',
        'phone',
        'location',
        'linkedin_url',
        'github_url',
        'image',
        'about_eyebrow',
        'about_heading_lead',
        'about_heading_accent',
        'about_strengths',
        'about_approach_steps',
        'about_values',
        'about_page_hero_eyebrow',
        'about_page_hero_title',
        'about_page_hero_description',
    ];

    protected function casts(): array
    {
        return [
            'strengths' => 'array',
            'about_strengths' => 'array',
            'about_approach_steps' => 'array',
            'about_values' => 'array',
        ];
    }
}
