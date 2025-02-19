<?php

namespace App\Bookings;

use App\Models\Employee;
use App\Models\Service;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Spatie\Period\Boundaries;
use Spatie\Period\Period;
use Spatie\Period\PeriodCollection;
use Spatie\Period\Precision;

class ScheduleAvailability
{
    protected PeriodCollection $periods;

    public function __construct(protected Employee $employee, protected Service $service)
    {
        $this->periods = new PeriodCollection();
    }

    public function forPeriod(Carbon $startAt, Carbon $endsAt)
    {


        collect(CarbonPeriod::create($startAt, $endsAt)->days())->each(
            function ($date) {

                $this->addAvailabilityFromSchedule($date);
            }
        );

        dd($this->periods);
    }

    protected function addAvailabilityFromSchedule(Carbon $date)
    {
        if (!$schedule = $this->employee->schedules->where('starts_at', '<=', $date)->where('ends_at', '>=', $date)->first()) {
            return;
        }

        if (![$startAt, $endsAt] = $schedule->getWorkingHoursForDate($date)) {
            return;
        }

        $this->periods  = $this->periods->add(
            Period::make(

                $date->copy()->setTimeFromTimeString($startAt),
                $date->copy()->setTimeFromTimeString($endsAt),
                Precision::MINUTE()
            )
        );
    }
}