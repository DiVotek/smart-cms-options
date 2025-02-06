<?php

namespace SmartCms\Options\Admin\Resources\OptionResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\ListRecords;
use SmartCms\Core\Services\Schema;
use SmartCms\Options\Admin\Resources\OptionResource;
use SmartCms\Options\Models\Option;

class ListOptions extends ListRecords
{
    protected static string $resource = OptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make(_hints('help'))
                ->iconButton()
                ->icon('heroicon-o-question-mark-circle')
                ->modalDescription(_hints('help.option'))
                ->modalFooterActions([]),
            Actions\Action::make('create')
                ->label(_actions('create'))
                ->form(function (Form $form) {
                    return $form->schema([
                        Schema::getName(),
                        Schema::getStatus(),
                        Toggle::make('required')->default(true)
                    ]);
                })->action(function (array $data) {
                    Option::query()->create([
                        'name' => $data['name'],
                        'status' => $data['status'],
                        'required' => $data['required'],
                    ]);
                }),
        ];
    }
}
