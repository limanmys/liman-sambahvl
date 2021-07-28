<?php
namespace App\Controllers;

class ClockController
{
	public function getHardwareClock(){
        $command="hwclock";
        $hwClock=runCommand(sudo() . $command);

        return respond($hwClock,200);
    }

    public function getSystemClock(){
        $command="date";
        $systemClock=runCommand(sudo() . $command);

        return respond($systemClock,200);
    }
}
