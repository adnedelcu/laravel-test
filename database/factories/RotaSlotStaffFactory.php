<?php

use Faker\Generator as Faker;

$factory->define(App\Modules\RotaSlotStaff\Models\RotaSlotStaff::class, function (Faker $faker) {
    $startDate = new DateTime('00:00:00');
    $endDate = new DateTime('23:59:59');

    $startTime = mt_rand($startDate->getTimestamp(), $endDate->getTimestamp());
    $endTime = mt_rand($startTime, $endDate->getTimestamp() + 24*60*60); // add 1 day to the end date

    return [
        'rotaid' => mt_rand(),
        'daynumber' => mt_rand(0, 6),
        'staffid' => mt_rand(0, 1) ? mt_rand(1, 10) : null,
        'slottype' => mt_rand(0, 1) ? 'shift' : 'dayoff',
        'starttime' => date('H:i:s', $startTime),
        'endttime' => date('H:i:s', $endTime),
        'workhours' => ($endTime - $startTime) / 3600,
        'premiumminutes' => 0,
        'roletypeid' => 11,
        'freeminutes' => 0,
        'seniorcashierminutes' => 0,
        'splitshifttimess' => '--:--*--:--',
    ];
});
