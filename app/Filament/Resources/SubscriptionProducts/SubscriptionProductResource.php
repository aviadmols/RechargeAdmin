<?php

namespace App\Filament\Resources\SubscriptionProducts;

use App\Filament\Resources\SubscriptionProducts\Pages\ManageSubscriptionProducts;
use App\Models\SubscriptionProduct;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionProductResource extends Resource
{
    protected static ?string $model = SubscriptionProduct::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'Subscription product';

    protected static ?string $pluralModelLabel = 'Subscription products';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Description')
                    ->rows(2)
                    ->columnSpanFull(),
                TextInput::make('shopify_variant_id')
                    ->label('Shopify Variant ID')
                    ->required()
                    ->helperText('The Shopify variant ID (gid or numeric). Used as external_variant_id when creating a Recharge subscription.'),
                TextInput::make('recharge_product_id')
                    ->label('Recharge Product ID')
                    ->helperText('Optional – for your reference.'),
                TextInput::make('image_url')
                    ->label('Image URL')
                    ->url()
                    ->maxLength(500),
                TextInput::make('order_interval_frequency')
                    ->label('Order interval (frequency)')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->helperText('e.g. 1 for "every 1 month".'),
                Select::make('order_interval_unit')
                    ->label('Order interval (unit)')
                    ->options([
                        'day' => 'Day',
                        'week' => 'Week',
                        'month' => 'Month',
                    ])
                    ->default('month'),
                TextInput::make('sort_order')
                    ->label('Sort order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower = first in list.'),
                Checkbox::make('is_active')
                    ->label('Active (shown in portal)')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('shopify_variant_id')
                    ->label('Shopify Variant ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('order_interval_frequency')
                    ->label('Interval')
                    ->formatStateUsing(fn ($record) => $record->order_interval_frequency . ' ' . $record->order_interval_unit)
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSubscriptionProducts::route('/'),
        ];
    }
}
