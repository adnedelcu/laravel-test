<?php

namespace App\Modules\RotaSlotStaff\Models;

use Illuminate\Database\Eloquent\Model;

class RotaSlotStaff extends Model
{
    protected $table = 'rota_slot_staff';

    public $timestamps = false;

    public function getShifts()
    {
        return $this->newQuery()
            ->whereNotNull('staffid')
            ->where('slottype', 'shift')
            ->get()
        ;
    }

    public function getStaffIds()
    {
        return $this->newQuery()
            ->distinct('staffid')
            ->whereNotNull('staffid')
            ->get(['staffid'])
        ;
    }
}
