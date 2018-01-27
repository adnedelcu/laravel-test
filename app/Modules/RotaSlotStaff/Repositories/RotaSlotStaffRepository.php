<?php

namespace App\Modules\RotaSlotStaff\Repositories;

use App\Modules\RotaSlotStaff\Models\RotaSlotStaff;
use Illuminate\Database\Eloquent\Collection;

/**
* RotaSlotStaff repository
*/
class RotaSlotStaffRepository
{
    /**
     * Rota slot staff model
     *
     * @var RotaSlotStaff
     */
    protected $model;

    /**
     * Constructor
     *
     * @param RotaSlotStaff $model
     */
    public function __construct(RotaSlotStaff $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve shifts
     *
     * @return array
     */
    public function getShifts()
    {
        $shifts = $this->model->getShifts();

        $shifts = $this->formatShifts($shifts);

        return $shifts;
    }

    /**
     * Retrieve staff ids
     *
     * @return array
     */
    public function getStaffIds()
    {
        $staffIds = $this->model->getStaffIds();

        $staffIds = array_map(function ($result) {
            return $result['staffid'];
        }, $staffIds->toArray());

        return $staffIds;
    }

    /**
     * Retrieve total worked hours grouped per day
     *
     * @param  array $shifts
     *
     * @return array
     */
    public function getTotalHours(array $shifts)
    {
        $totalHours = [];

        foreach ($shifts as $dayNumber => $shiftInfo) {
            $totalHours[$dayNumber] = array_sum(array_column($shifts[$dayNumber], 'work_hours'));
        }

        return $totalHours;
    }

    /**
     * Retrieve minutes worked alone grouped by days
     *
     * @param  array $shifts
     *
     * @return array
     */
    public function getMinutesAlone(array $shifts)
    {
        $minutesAlone = [];

        foreach ($shifts as $dayNumber => $shiftInfo) {
            uasort($shiftInfo, function ($shiftA, $shiftB) {
                $shiftAStart = new \DateTime($shiftA['start_time']);
                $shiftBStart = new \DateTime($shiftB['start_time']);

                return $shiftAStart <=> $shiftBStart;
            });

            $minutesAlone[$dayNumber] = $this->calculateWorkedAlone($shiftInfo);
        }

        return $minutesAlone;
    }

    /**
     * Format shift info grouped by day number
     *
     * @param  Collection $shifts
     *
     * @return array
     */
    protected function formatShifts(Collection $shifts)
    {
        $formattedShifts = [];

        foreach ($shifts as $shift) {
            $dayNumber = $shift->daynumber;

            if (!isset($formattedShifts[$dayNumber])) {
                $formattedShifts[$dayNumber] = [];
            }

            $formattedShifts[$shift->daynumber][$shift->staffid] = [
                'start_time' => $shift->starttime,
                'end_time' => $shift->endtime,
                'work_hours' => $shift->workhours,
            ];
        }

        return $formattedShifts;
    }

    /**
     * Calculate how many minutes have been worked alone in a day
     *
     * @param  array  $dayShifts
     *
     * @return int
     */
    protected function calculateWorkedAlone(array $dayShifts)
    {
        $minutesAlone = 0;
        $shiftsAlone = [];

        foreach ($dayShifts as $staffId => $shift) {
            $shiftRange = $this->getRange($shift['start_time'], $shift['end_time']);
            $shiftRanges = [$shiftRange];

            $shifts = $this->compareDayShifts($shift, $shiftRanges, $dayShifts);

            if ($shifts === []) {
                continue;
            }

            $shiftsAlone = array_merge($shiftsAlone, $shifts);
        }

        $shiftsAlone = array_values(array_filter($shiftsAlone, function ($shiftAlone, $index) use ($shiftsAlone) {
            for ($i = 0; $i < count($shiftsAlone); $i++) {
                if ($shiftAlone[0]->getTimestamp() === $shiftsAlone[$i][0]->getTimestamp() &&
                    $shiftAlone[1]->getTimestamp() === $shiftsAlone[$i][1]->getTimestamp() &&
                    $i != $index
                ) {
                    return false;
                }
            }

            return true;
        }, ARRAY_FILTER_USE_BOTH));

        $minutesAlone += $this->calculateMinutesAlone($shiftsAlone);

        return $minutesAlone;
    }

    /**
     * Compare shifts for a day
     *
     * @param  \DateTime[] $shift
     * @param  array $shiftsAlone
     * @param  array $shifts
     *
     * @return array
     */
    protected function compareDayShifts(array $shift, array $shiftsAlone, array $shifts)
    {
        $uniquePeriods = [];

        foreach ($shifts as $shiftInfo) {
            if ($shift === $shiftInfo) {
                continue;
            }

            $shiftRange = $this->getRange($shiftInfo['start_time'], $shiftInfo['end_time']);

            foreach ($shiftsAlone as $shiftAlone) {
                $uniquePeriods = array_merge($uniquePeriods, $this->getUniquePeriods($shiftAlone, $shiftRange));
            }

            if ($uniquePeriods === []) {
                return [];
            }
        }

        return $uniquePeriods;
    }

    /**
     * Retrieve unique periods between provided periods
     *
     * @param  \DateTime[]  $periodA
     * @param  \DateTime[]  $periodB
     *
     * @return array
     */
    protected function getUniquePeriods(array $periodA, array $periodB)
    {
        list($startA, $endA) = $periodA;
        list($startB, $endB) = $periodB;

        // check if there is no overlap between ranges
        if ($startA > $endB || $endA < $startB) {
            return [$periodA, $periodB];
        }

        // check if periods are the same
        if ($startA == $startB && $endA == $endB) {
            return [];
        }

        // check if is the periodA is inside periodB
        if ($startB < $startA && $endB > $endA) {
            return [[$startB, $startA], [$endA, $endB]];
        }

        // check if periodB is inside periodA
        if ($startA < $startB && $endB < $endA) {
            return [[$startA, $startB], [$endB, $endA]];
        }

        // check if periodA starts before periodB
        if ($startB < $startA) {
            return [[$startB, $startA]];
        }

        // periodA ends after periodB
        if ($endB < $endA) {
            return [[$endA, $endB]];
        }

        return [];
    }

    /**
     * Calculate number of minutes alone
     *
     * @param  \DateTime[] $shiftsAlone
     *
     * @return int
     */
    protected function calculateMinutesAlone($shiftsAlone)
    {
        $minutesAlone = 0;

        foreach ($shiftsAlone as $shiftAlone) {
            list($start, $end) = $shiftAlone;
            $diff = $end->getTimestamp() - $start->getTimestamp();

            $minutesAlone += $diff / 60;
        }

        return $minutesAlone;
    }

    /**
     * Get a combination of DateTime objects representing start and end times
     *
     * @param  string $startTime
     * @param  string $endTime
     *
     * @return \DateTime[]
     */
    protected function getRange($startTime, $endTime)
    {
        $start = new \DateTime($startTime);
        $end = new \DateTime($endTime);

        if ($start > $end) {
            $end->modify('+1 day');
        }

        return [$start, $end];
    }
}
