<?php

namespace App\Filament\Resources\PortalCustomers\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'auditLogs';

    protected static ?string $title = 'Activity log';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date & time')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
                TextColumn::make('action')
                    ->label('Action')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => self::actionLabel($state)),
                TextColumn::make('target_type')->label('Target type'),
                TextColumn::make('target_id')->label('Target ID'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'success' ? 'success' : 'danger'),
                TextColumn::make('message')->label('Message')->limit(50),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([])
            ->headerActions([])
            ->striped();
    }

    private static function actionLabel(string $action): string
    {
        return match ($action) {
            'portal.login' => 'Login to portal',
            'portal.login_otp' => 'Login (OTP)',
            'portal.page_view' => 'Page view',
            'add_product' => 'Add product to subscription',
            'buy_once' => 'Buy once (no subscription)',
            'address.update' => 'Update address',
            'subscription.next_charge_date' => 'Change next charge date',
            'subscription.cancel' => 'Cancel subscription',
            'subscription.pause' => 'Pause subscription',
            'subscription.resume' => 'Resume subscription',
            'subscription.swap' => 'Swap subscription',
            'subscription.quantity' => 'Update quantity',
            default => $action,
        };
    }
}
