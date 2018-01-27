<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRotaSlotStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rota_slot_staff', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rotaid');
            $table->tinyInteger('daynumber');
            $table->integer('staffid')->nullable()->default(null);
            $table->char('slottype', 20);
            $table->time('starttime')->nullable()->default(null);
            $table->time('endtime')->nullable()->default(null);
            $table->float('workhours', 4, 2);
            $table->integer('premiumminutes')->nullable()->default(null);
            $table->integer('roletypeid')->nullable()->default(null);
            $table->integer('freeminutes')->nullable()->default(null);
            $table->integer('seniorcashierminutes')->nullable()->default(null);
            $table->char('splitshifttimes', 11)->nullable()->default(null);
            $table->index(['rotaid', 'staffid'], 'rotaid');
            $table->index('daynumber', 'daynumber');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rota_slot_staff');
    }
}
