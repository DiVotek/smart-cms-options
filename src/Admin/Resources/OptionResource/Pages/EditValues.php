<?php

namespace SmartCms\Options\Admin\Resources\OptionResource\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\TableSchema;
use SmartCms\Options\Admin\Resources\OptionResource;
use SmartCms\Options\Models\OptionValue;

class EditValues extends ManageRelatedRecords
{
    protected static string $resource = OptionResource::class;

    protected static string $relationship = 'values';

    public static function getNavigationLabel(): string
    {
        return _nav('values');
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return 'heroicon-o-list-bullet';
    }

    public function getBreadcrumb(): string
    {
        return $this->record->name;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')->schema([
                    Schema::getName(),
                    Schema::getStatus(),
                    Schema::getSorting(),
                    Schema::getImage(name: 'image'),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TableSchema::getName(),
                TableSchema::getStatus(),
                TableSchema::getSorting(),
                TableSchema::getUpdatedAt(),
            ])
            ->reorderable('sorting')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data) {
                        $data['option_id'] = $this->record->id;
                        $record = OptionValue::query()->create($data);
                        return $record;
                        return [
                            'option_id' => $this->record->id,
                        ];
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->form(function ($form, $record) {
                    // dd($record);
                    return $form->schema([
                        Section::make('')->schema([
                            Schema::getName(),
                            Schema::getStatus(),
                            Schema::getSorting(),
                            Schema::getImage(name: 'image')->default($record['image']),
                        ]),
                    ]);
                }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make()->icon('heroicon-o-x-circle')->after(function ($record) {
                OptionValue::query()->where('option_id', $record->id)->delete();
            }),
            \Filament\Actions\Action::make(_actions('save_close'))
                ->label('Save & Close')
                ->icon('heroicon-o-check-badge')
                ->formId('form')
                ->action(function () {
                    $this->getOwnerRecord()->touch();

                    return redirect()->to(ListOptions::getUrl());
                }),
            \Filament\Actions\Action::make(_actions('save'))
                ->label(_actions('save'))
                ->icon('heroicon-o-check-circle')
                ->formId('form')
                ->action(function () {
                    $this->getOwnerRecord()->touch();
                }),
        ];
    }
}
