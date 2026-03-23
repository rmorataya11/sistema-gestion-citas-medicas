<?php

namespace App\Filament\Pages;

use App\Models\Appointment;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class MonthlyCalendar extends Page
{
    protected string $view = 'filament.pages.monthly-calendar';

    protected static ?string $navigationLabel = 'Calendario';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Gestion clinica';

    protected static ?int $navigationSort = 5;

    public int $month;

    public int $year;

    public function mount(): void
    {
        $this->month = (int) now()->month;
        $this->year = (int) now()->year;
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->can('viewAny', Appointment::class);
    }

    public function previousMonth(): void
    {
        $d = \Carbon\Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->month = (int) $d->month;
        $this->year = (int) $d->year;
    }

    public function nextMonth(): void
    {
        $d = \Carbon\Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->month = (int) $d->month;
        $this->year = (int) $d->year;
    }

    public function getHeading(): string
    {
        return 'Calendario de citas';
    }
}
