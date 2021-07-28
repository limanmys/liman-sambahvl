<?php
namespace App\Utils\Command;
use Liman\Toolkit\Shell\ICommandEngine;

class LimanRunnerEngine implements ICommandEngine
{
    public static function run($command)
    {
        return shell_exec($command);
    }

    public static function sudo()
    {
        return "";
    }
}