<?php

namespace App\Filament\Support;

use Filament\Actions\Action;

final class PortfolioEditorActions
{
    /**
     * SweetAlert2 confirmation instead of the default Filament modal (via data attributes + JS).
     */
    public static function confirmedDelete(string $title, ?string $text = null): \Closure
    {
        return fn (Action $action) => $action
            ->requiresConfirmation(false)
            ->extraAttributes(self::sweetConfirmAttributes($title, $text));
    }

    /**
     * @return array<string, string>
     */
    public static function sweetConfirmAttributes(
        string $title,
        ?string $text = null,
        ?string $confirmText = null,
        ?string $cancelText = null,
    ): array {
        return [
            'data-swal-confirm' => 'true',
            'data-swal-title' => $title,
            'data-swal-text' => $text ?? 'This action cannot be undone.',
            'data-swal-confirm-text' => $confirmText ?? 'Yes, continue',
            'data-swal-cancel-text' => $cancelText ?? 'Cancel',
        ];
    }
}
