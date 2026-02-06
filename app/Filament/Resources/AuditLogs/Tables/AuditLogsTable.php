<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('actor_email')->label('Email')->searchable(),
                TextColumn::make('recharge_customer_id')->label('Customer ID'),
                TextColumn::make('action')->label('Action')->searchable(),
                TextColumn::make('target_type'),
                TextColumn::make('target_id'),
                TextColumn::make('status')->badge(),
                TextColumn::make('message')->limit(40),
                TextColumn::make('created_at')->label('Date')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('action')->options(fn () => \App\Models\AuditLog::query()->distinct()->pluck('action', 'action')->all()),
                SelectFilter::make('status')->options(['success' => 'Success', 'fail' => 'Fail']),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
