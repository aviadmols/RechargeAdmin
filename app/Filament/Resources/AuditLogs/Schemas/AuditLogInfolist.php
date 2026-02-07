<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('action')->label('Action')->badge(),
                TextEntry::make('status')->label('Status')->badge(),
                TextEntry::make('actor_email')->label('Email'),
                TextEntry::make('recharge_customer_id')->label('Customer ID'),
                TextEntry::make('target_type')->label('Target type'),
                TextEntry::make('target_id')->label('Target ID'),
                TextEntry::make('message')->label('Message')->columnSpanFull(),
                TextEntry::make('created_at')->label('Date')->dateTime(),
                TextEntry::make('metadata')
                    ->label('Details (metadata)')
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : (string) $state)
                    ->columnSpanFull()
                    ->copyable()
                    ->copyMessage('Copied'),
            ]);
    }
}
