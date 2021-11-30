<?php
namespace App\Controllers;

use Liman\Toolkit\Shell\Command;

class ClockController
{
	public function getHardwareClock()
    {
        $hwClock = Command::runSudo("hwclock");
        return respond($hwClock,200);
    }

    public function getSystemClock()
    {
        $systemClock=Command::runSudo("date");
        return respond($systemClock,200);
    }
}
