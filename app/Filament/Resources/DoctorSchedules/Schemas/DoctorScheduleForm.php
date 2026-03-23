<?php

namespace App\Filament\Resources\DoctorSchedules\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class DoctorScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Horario')
                    ->schema([
                        Select::make('id_doctor')
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
                        Select::make('day_of_week')
                            ->label('Dia')
                            ->options([
                                0 => 'Domingo',
                                1 => 'Lunes',
                                2 => 'Martes',
                                3 => 'Miercoles',
                                4 => 'Jueves',
                                5 => 'Viernes',
                                6 => 'Sabado',
                            ])
                            ->required(),
                        TimePicker::make('start_time')
                            ->label('Inicio')
                            ->required(),
                        TimePicker::make('end_time')
                            ->label('Fin')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
