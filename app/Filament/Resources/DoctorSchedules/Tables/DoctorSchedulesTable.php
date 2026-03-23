<?php

namespace App\Filament\Resources\DoctorSchedules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DoctorSchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('doctor.name')
                    ->label('Medico')
                    ->searchable(),
                TextColumn::make('day_of_week')
                    ->label('Dia')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Domingo',
                        1 => 'Lunes',
                        2 => 'Martes',
                        3 => 'Miercoles',
                        4 => 'Jueves',
                        5 => 'Viernes',
                        6 => 'Sabado',
                        default => (string) $state,
                    })
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label('Inicio')
                    ->time()
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label('Fin')
                    ->time()
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
