<?php

namespace App\Tasks;

use Liman\Toolkit\Formatter;
use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\RemoteTask\Task;

class InstallDependencies extends Task
{
	protected $description = 'Installing dependencies...';
	protected $sudoRequired = true;

	public function __construct(array $attrbs=[])
	{

		$this->control = Distro::debian('apt|dpkg')
			->get();

		$this->command = Distro::debian(
            "apt install -y gnupg2 && apt install -y ca-certificates"
            )
			->get();
		

		$this->attributes = $attrbs;
		$this->logFile = Formatter::run('/tmp/smbHvlLog.txt');
	}
}

		
	
	
		
		
