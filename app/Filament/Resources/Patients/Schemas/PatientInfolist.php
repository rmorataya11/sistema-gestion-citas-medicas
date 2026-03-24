<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PatientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos personales')
                    ->schema([
                        TextEntry::make('first_name')
                            ->label('Nombre'),
                        TextEntry::make('last_name')
                            ->label('Apellidos'),
                        TextEntry::make('dui')
                            ->label('DUI'),
                        TextEntry::make('birth_date')
                            ->label('Fecha nacimiento')
                            ->date(),
                        TextEntry::make('phone')
                            ->label('Telefono'),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                    ])
                    ->columns(2),
                Section::make('Expediente clinico')
                    ->schema([
                        TextEntry::make('medicalRecord.blood_type')
                            ->label('Grupo sanguineo')
                            ->placeholder('-'),
                        TextEntry::make('medicalRecord.allergies')
                            ->label('Alergias')
                            ->placeholder('-'),
                        TextEntry::make('medicalRecord.family_history')
                            ->label('Antecedentes familiares')
                            ->placeholder('-'),
                    ])
                    ->columns(1),
            ]);
    }
}
