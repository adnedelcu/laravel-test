<?php

namespace Tests\Unit;

use App\Modules\RotaSlotStaff\Models\RotaSlotStaff;
use App\Modules\RotaSlotStaff\Repositories\RotaSlotStaffRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
* RotaSlotStaffRepository unit test class
*/
class RotaSlotStaffRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A test for retrieving staffIds from database
     *
     * @return void
     */
    public function testGetStaffIds()
    {
        $repo = new RotaSlotStaffRepository();

        $users = factory(RotaSlotStaff::class, 10)->make();

        $staffIds = $repo->getStaffIds();

        $this->assertCount(10, $staffIds);
    }
}
