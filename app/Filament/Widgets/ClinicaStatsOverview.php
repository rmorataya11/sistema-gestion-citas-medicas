<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Patient;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClinicaStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pacientes totales', (string) Patient::query()->count()),
            Stat::make('Citas hoy', (string) Appointment::query()
                ->whereDate('appointment_date', today())
                ->when(
                    auth()->user()?->hasRole('doctor'),
                    fn ($q) => $q->where('doctor_id', auth()->id())
                )
                ->count()),
        ];
    }
}
