<?php

use Illuminate\Database\Seeder;
use Symfony\Component\Yaml\Yaml;

class RotaSlotStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rotaSlotStaffEntries = Yaml::parse(file_get_contents(database_path('fixtures/rota_slot_staff.yml')));

        DB::table('rota_slot_staff')->insert($rotaSlotStaffEntries);
    }
}
