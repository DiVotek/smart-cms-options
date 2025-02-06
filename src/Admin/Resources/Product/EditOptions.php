<?php

namespace SmartCms\Options\Admin\Resources\Product;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use SmartCms\Options\Models\Option;
use SmartCms\Options\Models\OptionValue;
use SmartCms\Store\Admin\Resources\ProductResource;
use SmartCms\Store\Admin\Resources\ProductResource\Pages\ListProducts;
use SmartCms\Store\Models\CartItem;
use SmartCms\Store\Services\Calculator;
use SmartCms\Store\Services\CartService;
use SmartCms\Store\Services\TableSchema;

class EditOptions extends ManageRelatedRecords
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'optionValues';

    public static function getNavigationLabel(): string
    {
        return _nav('options');
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return 'heroicon-o-scale';
    }

    public function getBreadcrumb(): string
    {
        return $this->record->name;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('option.name'),
                Tables\Columns\TextColumn::make('sign'),
                Tables\Columns\TextColumn::make('price')->numeric()->suffix(" " . app('front_currency')->code ?? ''),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label(_actions('add'))
                    ->form(fn(AttachAction $action): array => [
                        $action->getRecordSelect(),
                        ToggleButtons::make('sign')->options([
                            '+' => 'Plus',
                            '-' => 'Minus',
                        ])->inline()->default('+'),
                        TextInput::make('origin_price')
                            ->default(0)->required()
                            ->numeric()->suffix(app('currency')->code ?? '')->live(debounce: 1000)->afterStateUpdated(function ($state, $set) {
                                $set('price', Calculator::calculate($state));
                            }),
                        TextInput::make('price')->readOnly()->default(0)->required()
                            ->numeric()->suffix(app('front_currency')->code ?? '')->live()
                            ->disabled()
                            ->hidden(function () {
                                return app('front_currency')->id == app('currency')->id;
                            }),
                    ])->mutateFormDataUsing(function ($data) {
                        $data['price'] = Calculator::calculate($data['origin_price']);
                        return $data;
                    })
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()->label(_actions('delete')),
                Tables\Actions\EditAction::make()->form([
                    Hidden::make('option_value_id'),
                    Hidden::make('product_id'),
                    ToggleButtons::make('sign')->options([
                        '+' => 'Plus',
                        '-' => 'Minus',
                    ])->inline()->default('+'),
                    TextInput::make('origin_price')
                        ->default(0)->required()
                        ->numeric()->suffix(app('currency')->code ?? '')->live(debounce: 1000)->afterStateUpdated(function ($state, $set) {
                            $set('price', Calculator::calculate($state));
                        }),
                    TextInput::make('price')->readOnly()->default(0)->required()
                        ->numeric()->suffix(app('front_currency')->code ?? '')->live()
                        ->disabled()
                        ->hidden(function () {
                            return app('front_currency')->id == app('currency')->id;
                        }),
                ])->action(function ($data) {
                    $data['price'] = Calculator::calculate($data['origin_price']);
                    DB::table(sconfig('database_table_prefix', 'smart_cms_') . 'product_options')->where('option_value_id', $data['option_value_id'])->where('product_id', $data['product_id'])->update([
                        'sign' => $data['sign'],
                        'origin_price' => $data['origin_price'],
                        'price' => $data['price'],
                    ]);
                    $option = OptionValue::query()->find($data['option_value_id'])->option_id;
                    $cartItems = CartItem::query()->where('product_id', $data['product_id'])->get();
                    foreach ($cartItems as $cartItem) {
                        if ($cartItem->extra && is_array($cartItem->extra) && isset($cartItem->extra['options'])) {
                            $options = $cartItem->extra['options'];
                            foreach ($options as $key => $value) {
                                if ($key == $option && $value == $data['option_value_id']) {
                                    CartService::recalculateItemPrice($cartItem);
                                    continue (2);
                                }
                            }
                        }
                    }
                    Notification::make()
                        ->title(_actions('success'))
                        ->success()
                        ->send();
                }),
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
            \Filament\Actions\DeleteAction::make()->icon('heroicon-o-x-circle'),
            \Filament\Actions\ViewAction::make()
                ->url(fn($record) => $record->route())
                ->icon('heroicon-o-arrow-right-end-on-rectangle')
                ->openUrlInNewTab(true),
            \Filament\Actions\Action::make(_actions('save_close'))
                ->label('Save & Close')
                ->icon('heroicon-o-check-badge')
                ->formId('form')
                ->action(function () {
                    $this->getOwnerRecord()->touch();

                    return redirect()->to(ListProducts::getUrl());
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
