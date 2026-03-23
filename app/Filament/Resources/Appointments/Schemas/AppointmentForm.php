<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Models\Patient;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('patient_id')
                    ->label('Paciente')
                    ->relationship(
                        'patient',
                        'first_name',
                        fn (Builder $query) => $query->orderBy('last_name')
                    )
                    ->getOptionLabelFromRecordUsing(fn (Patient $record): string => $record->first_name.' '.$record->last_name)
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('doctor_id')
                    ->label('Medico')
                    ->relationship(
                        'doctor',
                        'name',
                        fn (Builder $query) => $query->where('role', 'doctor')
                    )
                    ->searchable()
                    ->preload()
                    ->visible(fn (): bool => auth()->user()->hasAnyRole(['admin', 'assistant']))
                    ->required(fn (): bool => auth()->user()->hasAnyRole(['admin', 'assistant'])),
                DateTimePicker::make('appointment_date')
                    ->label('Fecha y hora')
                    ->required(),
                Textarea::make('reason')
                    ->label('Motivo')
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                    ])
                    ->required(),
            ]);
    }
}
