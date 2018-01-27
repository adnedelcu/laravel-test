<?php

namespace App\Http\Controllers;

use App\Modules\RotaSlotStaff\Repositories\RotaSlotStaffRepository;
use Illuminate\Http\Request;

class RotaSlotStaffController extends Controller
{
    /**
     * Repositoru for shifts
     *
     * @var RotaSlotStaffRepository
     */
    protected $repo;

    /**
     * Constructor
     *
     * @param RotaSlotStaffRepository $repository
     */
    public function __construct(RotaSlotStaffRepository $repository)
    {
        $this->repo = $repository;
    }

    public function index()
    {
        $shifts = $this->repo->getShifts();
        $staffIds = $this->repo->getStaffIds();
        $totalHours = $this->repo->getTotalHours($shifts);
        $minutesAlone = $this->repo->getMinutesAlone($shifts);

        return view('shifts.index', [
            'shifts' => $shifts,
            'staffIds' => $staffIds,
            'totalHours' => $totalHours,
            'minutesAlone' => $minutesAlone,
        ]);
    }
}
