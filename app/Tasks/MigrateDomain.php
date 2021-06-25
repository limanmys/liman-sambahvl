<?php

namespace App\Tasks;

use Liman\Toolkit\Formatter;
use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\RemoteTask\Task;
use Liman\Toolkit\Shell\Command;

class MigrateDomain extends Task
{
	protected $description = 'Installing package...';
	protected $sudoRequired = true;

	public function __construct(array $attrbs=[])
	{
		$this->control = Distro::debian('apt|dpkg')
			->get();

		$this->command = Distro::debian(
			"smb-migrate-domain -s ". $attributes['ip'] ." -a " . $attributes['username'] . " -p ". $attributes['password'] ." 2>&1 > /tmp/smbMigrateLog.txt"
            )
			->get();

		$this->attributes = $attrbs;
		$this->logFile = Formatter::run('/tmp/smbMigrateLog.txt');
	}
}

/*

swal("Good job!", "You clicked the button!", "success")




*/

		
	
	
		
		
