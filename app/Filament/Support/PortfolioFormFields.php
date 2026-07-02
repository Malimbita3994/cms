<?php

namespace App\Filament\Support;

use App\Support\PortfolioAsset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;

final class PortfolioFormFields
{
    /**
     * @deprecated Use {@see richToolbarButtons()} instead.
     *
     * @var array<int, array<int, string|ToolbarButtonGroup>>
     */
    public const RICH_TOOLBAR = [
        ['bold', 'italic', 'underline', 'link'],
        ['alignStart', 'alignCenter', 'alignEnd', 'alignJustify'],
        ['bulletList', 'orderedList'],
        ['undo', 'redo'],
    ];

    /**
     * Shared TipTap toolbar: formatting, text alignment, lists, history.
     *
     * @return array<int, array<int, string|ToolbarButtonGroup>>
     */
    public static function richToolbarButtons(): array
    {
        return [
            ['bold', 'italic', 'underline', 'link'],
            [
                ToolbarButtonGroup::make('Alignment', [
                    'alignStart',
                    'alignCenter',
                    'alignEnd',
                    'alignJustify',
                ]),
            ],
            ['bulletList', 'orderedList'],
            ['undo', 'redo'],
        ];
    }

    /**
     * Toolbar + floating alignment controls (used on create/edit and singleton editors).
     */
    public static function applyRichEditorDefaults(RichEditor $editor): RichEditor
    {
        return $editor
            ->toolbarButtons(self::richToolbarButtons())
            ->floatingToolbars([
                'paragraph' => [
                    'bold',
                    'italic',
                    'underline',
                    'link',
                    'alignStart',
                    'alignCenter',
                    'alignEnd',
                    'alignJustify',
                ],
                'heading' => [
                    'alignStart',
                    'alignCenter',
                    'alignEnd',
                    'alignJustify',
                ],
            ]);
    }

    public static function imageUpload(
        string $name,
        string $directory,
        string $label,
        int $previewHeight,
        string $helperText,
        bool $required = true,
    ): FileUpload {
        $upload = FileUpload::make($name)
            ->label($label)
            ->disk(PortfolioAsset::DISK)
            ->directory($directory)
            ->visibility('public')
            ->image()
            ->imageEditor()
            ->openable()
            ->downloadable()
            ->previewable()
            ->imagePreviewHeight($previewHeight)
            ->panelLayout('compact')
            ->removeUploadedFileButtonPosition('right')
            ->uploadButtonPosition('center')
            ->loadingIndicatorPosition('center')
            ->uploadProgressIndicatorPosition('center')
            ->maxSize(12_288)
            ->getUploadedFileUsing(PortfolioAsset::uploadedFileResolver(...))
            ->helperText($helperText);

        if ($required) {
            $upload->required();
        }

        return $upload;
    }

    public static function pdfUpload(
        string $name,
        string $directory,
        string $label,
        string $helperText,
        bool $required = false,
    ): FileUpload {
        $upload = FileUpload::make($name)
            ->label($label)
            ->disk(PortfolioAsset::DISK)
            ->directory($directory)
            ->visibility('public')
            ->acceptedFileTypes(['application/pdf'])
            ->openable()
            ->downloadable()
            ->previewable(false)
            ->panelLayout('compact')
            ->removeUploadedFileButtonPosition('right')
            ->uploadButtonPosition('center')
            ->loadingIndicatorPosition('center')
            ->uploadProgressIndicatorPosition('center')
            ->maxSize(20_480)
            ->getUploadedFileUsing(PortfolioAsset::uploadedFileResolver(...))
            ->helperText($helperText);

        if ($required) {
            $upload->required();
        }

        return $upload;
    }

    public static function richEditor(string $name, string $label, ?string $placeholder = null): RichEditor
    {
        $editor = RichEditor::make($name)
            ->label($label)
            ->required()
            ->placeholder($placeholder)
            ->columnSpanFull();

        return self::applyRichEditorDefaults($editor);
    }
}
