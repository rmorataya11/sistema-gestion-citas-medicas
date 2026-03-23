<?php

namespace App\Filament\Resources\Appointments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AppointmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('patient.id')
                    ->label('Patient'),
                TextEntry::make('doctor.name')
                    ->label('Doctor'),
                TextEntry::make('appointment_date')
                    ->dateTime(),
                TextEntry::make('reason')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('status'),
            ]);
    }
}
