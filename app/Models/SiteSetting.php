<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'app_name',
        'site_title',
        'site_description',
        'site_url',
    ];
}
