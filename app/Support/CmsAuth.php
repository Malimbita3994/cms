<?php

namespace App\Support;

final class CmsAuth
{
    public const DOCUMENT_TITLE = 'CMS';

    public const BRAND_LABEL = 'BA CMS';

    public static function loginUrl(): string
    {
        return url('/');
    }

    public static function passwordResetUrl(): string
    {
        return url('/admin/password-reset');
    }
}
