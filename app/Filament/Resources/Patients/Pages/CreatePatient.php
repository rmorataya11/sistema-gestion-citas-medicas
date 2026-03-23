<?php

namespace App\Filament\Resources\Patients\Pages;

use App\Filament\Resources\Patients\PatientResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;

    public array $medicalRecordFields = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->medicalRecordFields = Arr::only($data, ['mr_blood_type', 'mr_allergies', 'mr_family_history']);

        return Arr::except($data, ['mr_blood_type', 'mr_allergies', 'mr_family_history']);
    }

    protected function afterCreate(): void
    {
        $attrs = array_filter([
            'blood_type' => $this->medicalRecordFields['mr_blood_type'] ?? null,
            'allergies' => $this->medicalRecordFields['mr_allergies'] ?? null,
            'family_history' => $this->medicalRecordFields['mr_family_history'] ?? null,
        ], fn ($v) => filled($v));

        if ($attrs !== []) {
            $this->record->medicalRecord()->create($attrs);
        }
    }
}
