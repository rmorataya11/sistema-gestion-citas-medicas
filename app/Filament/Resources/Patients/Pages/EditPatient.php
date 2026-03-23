<?php

namespace App\Filament\Resources\Patients\Pages;

use App\Filament\Resources\Patients\PatientResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;

class EditPatient extends EditRecord
{
    protected static string $resource = PatientResource::class;

    public array $medicalRecordFields = [];

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $r = $this->record->medicalRecord;
        if ($r) {
            $data['mr_blood_type'] = $r->blood_type;
            $data['mr_allergies'] = $r->allergies;
            $data['mr_family_history'] = $r->family_history;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->medicalRecordFields = Arr::only($data, ['mr_blood_type', 'mr_allergies', 'mr_family_history']);

        return Arr::except($data, ['mr_blood_type', 'mr_allergies', 'mr_family_history']);
    }

    protected function afterSave(): void
    {
        $attrs = [
            'blood_type' => $this->medicalRecordFields['mr_blood_type'] ?? null,
            'allergies' => $this->medicalRecordFields['mr_allergies'] ?? null,
            'family_history' => $this->medicalRecordFields['mr_family_history'] ?? null,
        ];

        $this->record->medicalRecord()->updateOrCreate(
            ['patient_id' => $this->record->id],
            $attrs
        );
    }
}
