<?php

namespace SmartCms\Options\Admin\Resources;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use SmartCms\Core\Models\Translate;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\TableSchema;
use SmartCms\Options\Admin\Resources\OptionResource\Pages as Pages;
use SmartCms\Options\Models\Option;

class OptionResource extends Resource
{
    protected static ?string $model = Option::class;

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): ?string
    {
        return _nav('catalog');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getModelLabel(): string
    {
        return __('options::trans.nav');
    }

    public static function getPluralModelLabel(): string
    {
        return __('options::trans.nav_plural');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')->schema([
                    Schema::getName()->suffixActions([
                        Action::make(_fields('translates'))
                            ->hidden(function ($record) {
                                return ! is_multi_lang() || $record == null;
                            })
                            ->icon(function ($record) {
                                if ($record->translatable()->count() > 0) {
                                    return 'heroicon-o-check-circle';
                                }

                                return 'heroicon-o-exclamation-circle';
                            })
                            ->form(function ($form) {
                                $fields = [];
                                $languages = get_active_languages();
                                foreach ($languages as $language) {
                                    $fields[] = TextInput::make($language->slug . '.name')->label(_fields('name') . ' (' . $language->name . ')');
                                }

                                return $form->schema($fields);
                            })->fillForm(function ($record) {
                                $translates = [];
                                $languages = get_active_languages();
                                foreach ($languages as $language) {
                                    $translates[$language->slug] = [
                                        'name' => $record->translatable()->where('language_id', $language->id)->first()->value ?? '',
                                    ];
                                }

                                return $translates;
                            })->action(function ($record, $data) {
                                foreach (get_active_languages() as $lang) {
                                    $name = $data[$lang->slug]['name'] ?? '';
                                    if ($name == '') {
                                        Translate::query()->where([
                                            'language_id' => $lang->id,
                                            'entity_id' => $record->id,
                                            'entity_type' => Option::class,
                                        ])->delete();

                                        continue;
                                    }
                                    Translate::query()->updateOrCreate([
                                        'language_id' => $lang->id,
                                        'entity_id' => $record->id,
                                        'entity_type' => Option::class,
                                    ], ['value' => $name]);
                                }
                                Notification::make()->success()->title(_actions('saved'))->send();
                            }),
                    ]),
                    Schema::getStatus(),
                    Schema::getSorting(),
                    Toggle::make('required')->label(_columns('is_required'))->default(false),
                    Select::make('default_value')->relationship('defaultValue', 'name')->label(_columns('default_value'))->nullable()->native(false),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableSchema::getName(),
                TableSchema::getStatus(),
                TableSchema::getSorting(),
                TableSchema::getUpdatedAt(),
                //
            ])
            ->reorderable('sorting')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOptions::route('/'),
            'create' => Pages\CreateOption::route('/create'),
            'edit' => Pages\EditOption::route('/{record}/edit'),
            'values' => Pages\EditValues::route('/{record}/values'),
        ];
    }

    public static function getRecordSubNavigation($page): array
    {
        return $page->generateNavigationItems([
            Pages\EditOption::class,
            Pages\EditValues::class,
        ]);
    }

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;
}
