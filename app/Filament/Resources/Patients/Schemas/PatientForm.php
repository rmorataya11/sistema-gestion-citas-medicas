<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('last_name')
                    ->label('Apellidos')
                    ->required(),
                TextInput::make('dni')
                    ->label('DNI')
                    ->required(),
                DatePicker::make('birth_date')
                    ->label('Fecha nacimiento')
                    ->required(),
                TextInput::make('phone')
                    ->label('Telefono')
                    ->tel()
                    ->required(),
                Section::make('Expediente clinico')
                    ->schema([
                        TextInput::make('mr_blood_type')
                            ->label('Grupo sanguineo'),
                        Textarea::make('mr_allergies')
                            ->label('Alergias')
                            ->rows(2),
                        Textarea::make('mr_family_history')
                            ->label('Antecedentes familiares')
                            ->rows(2),
                    ])
                    ->columns(1),
            ]);
    }
}
