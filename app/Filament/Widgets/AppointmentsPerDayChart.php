<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Widgets\ChartWidget;

class AppointmentsPerDayChart extends ChartWidget
{
    protected ?string $heading = 'Citas por dia (ultimos 7 dias)';

    protected function getData(): array
    {
        $labels = [];
        $counts = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->startOfDay();
            $labels[] = $day->format('d/m');
            $counts[] = Appointment::query()
                ->whereDate('appointment_date', $day)
                ->when(
                    auth()->user()?->hasRole('doctor'),
                    fn ($q) => $q->where('doctor_id', auth()->id())
                )
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Citas',
                    'data' => $counts,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
