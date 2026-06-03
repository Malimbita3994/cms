<?php

namespace App\Filament\Resources\Skills\Schemas;

use Filament\Schemas\Schema;

/**
 * @deprecated Use AdminSkillForm on create/edit pages.
 */
class SkillForm
{
    public static function configure(Schema $schema): Schema
    {
        return AdminSkillForm::configure($schema);
    }
}
