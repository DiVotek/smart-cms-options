<?php

namespace SmartCms\Options\Admin\Resources\OptionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use SmartCms\Options\Admin\Resources\OptionResource;

class EditOption extends EditRecord
{
    protected static string $resource = OptionResource::class;

    public static function getNavigationLabel(): string
    {
        return _nav('general');
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return 'heroicon-o-cog';
    }

    public function getBreadcrumb(): string
    {
        return $this->record->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make()->icon('heroicon-o-x-circle'),
            \Filament\Actions\Action::make(_actions('save_close'))
                ->label('Save & Close')
                ->icon('heroicon-o-check-badge')
                ->formId('form')
                ->action(function () {
                    $this->save(true, true);
                    $this->record->touch();

                    return redirect()->to(ListOptions::getUrl());
                }),
            $this->getSaveFormAction()
                ->label(_actions('save'))
                ->icon('heroicon-o-check-circle')
                ->action(function () {
                    $this->save();
                    $this->record->touch();
                })
                ->formId('form'),

        ];
    }
}
