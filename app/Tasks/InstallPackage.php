<?php

namespace App\Tasks;

use Liman\Toolkit\Formatter;
use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\RemoteTask\Task;
use Liman\Toolkit\Shell\Command;

class InstallPackage extends Task
{
	protected $description = 'Installing package...';
	protected $sudoRequired = true;

	public function __construct(array $attrbs=[])
	{

		$this->control = Distro::debian('apt|dpkg')
			->get();

		$this->command = Distro::debian(
			"echo 'deb [arch=amd64] http://depo.aciklab.org/ bullseye main' | sudo tee /etc/apt/sources.list.d/acikdepo.list && wget --no-check-certificate -qO - http://depo.aciklab.org/public.key | sudo apt-key add - && apt update && bash -c 'DEBIAN_FRONTEND=noninteractive apt install sambahvl -qqy >/tmp/smbHvlLog.txt 2>&1 & disown'"
		)
			->get();
		

		$this->attributes = $attrbs;
		$this->logFile = Formatter::run('/tmp/smbHvlLog.txt');
	}
}

		
	
	
		
		
