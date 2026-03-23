<x-filament-panels::page>
    <style>
        .calendar-grid th,
        .calendar-grid td {
            border: 1px solid rgb(228 228 231);
        }

        .dark .calendar-grid th,
        .dark .calendar-grid td {
            border-color: rgb(63 63 70);
        }
    </style>
    @php
        $start = \Carbon\Carbon::create($year, $month, 1)->startOfDay();
        $q = \App\Models\Appointment::query()->whereBetween('appointment_date', [$start, $start->copy()->endOfMonth()->endOfDay()])->with(['patient', 'doctor']);
        if (auth()->user()->hasRole('doctor')) {
            $q->where('doctor_id', auth()->id());
        }
        $byDay = $q->get()->groupBy(fn ($a) => $a->appointment_date->format('Y-m-d'));
        $daysInMonth = $start->daysInMonth;
        $firstDow = (int) $start->format('w');

        $cells = [];
        for ($i = 0; $i < $firstDow; $i++) {
            $cells[] = null;
        }
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $cells[] = $d;
        }
        while (count($cells) % 7 !== 0) {
            $cells[] = null;
        }
        $rows = array_chunk($cells, 7);
    @endphp

    <div class="space-y-4">
        <div class="flex flex-wrap items-center gap-2">
            <x-filament::button wire:click="previousMonth" type="button" size="sm">
                Mes anterior
            </x-filament::button>
            <x-filament::button wire:click="nextMonth" type="button" size="sm">
                Mes siguiente
            </x-filament::button>
            <span class="text-sm font-medium text-gray-950 dark:text-white">
                {{ str_pad((string) $month, 2, '0', STR_PAD_LEFT) }}/{{ $year }}
            </span>
        </div>

        <table
            style="width: 100%; border-collapse: collapse; table-layout: fixed;"
            class="calendar-grid text-sm text-gray-950 dark:text-white"
        >
            <thead>
                <tr>
                    @foreach (['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'] as $wd)
                        <th style="padding: 0.5rem; text-align: center; font-weight: 600;">
                            {{ $wd }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        @foreach ($row as $day)
                            <td style="vertical-align: top; min-height: 5rem; padding: 0.35rem;">
                                @if ($day === null)
                                    <span class="opacity-0">.</span>
                                @else
                                    @php
                                        $dateKey = \Carbon\Carbon::create($year, $month, $day)->format('Y-m-d');
                                        $items = $byDay->get($dateKey, collect());
                                    @endphp
                                    <div class="font-semibold">{{ $day }}</div>
                                    <ul style="margin: 0.25rem 0 0; padding-left: 0; list-style: none;">
                                        @foreach ($items->take(4) as $appt)
                                            <li
                                                style="font-size: 10px; margin-bottom: 2px; padding: 2px 4px; border-radius: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; background: rgba(245, 158, 11, 0.15);"
                                            >
                                                {{ $appt->appointment_date->format('H:i') }}
                                                @if ($appt->doctor)
                                                    — {{ $appt->doctor->name }}
                                                @endif
                                            </li>
                                        @endforeach
                                        @if ($items->count() > 4)
                                            <li style="font-size: 10px; color: rgb(113 113 122);">
                                                +{{ $items->count() - 4 }} mas
                                            </li>
                                        @endif
                                    </ul>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
