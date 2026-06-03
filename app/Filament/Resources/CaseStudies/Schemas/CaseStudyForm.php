<?php

namespace App\Filament\Resources\CaseStudies\Schemas;

use Filament\Schemas\Schema;

/**
 * @deprecated Use AdminCaseStudyForm on create/edit pages.
 */
class CaseStudyForm
{
    public static function configure(Schema $schema): Schema
    {
        return AdminCaseStudyForm::configure($schema);
    }
}
