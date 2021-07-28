<?php

use Liman\Toolkit\Shell\Command;
use App\Utils\Command\LimanRunnerEngine;

if (!function_exists('checkPort')) {
	function checkPort($ip, $port)
	{
		restoreHandler();
		if ($port == -1) {
			return true;
		}
		$fp = @fsockopen($ip, $port, $errno, $errstr, 0.1);
		setHandler();
		if (!$fp) {
			return false;
		} else {
			fclose($fp);
			return true;
		}
	}
}

if (!function_exists('certificateExists')) {
    function certificateExists($ip, $port)
    {
        $filename = "liman-" . strtolower($ip) . "_" . $port . ".crt";
        $path = "/usr/local/share/ca-certificates/";
        $flag = current(preg_grep("/".preg_quote($filename)."/i", glob("$path/*")));
        if(!$flag){
                $filename = "liman-" . gethostbyname($ip) . "_" . $port . ".crt";
                return current(preg_grep("/".preg_quote($filename)."/i", glob("$path/*")));
        }
        return $flag;
    }
}

if (!function_exists('isCertificateValid')) {
    function isCertificateValid($ip, $port)
    {
        Command::bindEngine(LimanRunnerEngine::class);
        if (certificateExists($ip, $port)) {
            $srv_cert = Command::run("openssl s_client -connect {:ip}:{:port} 2>/dev/null </dev/null |  sed -ne '/-BEGIN CERTIFICATE-/,/-END CERTIFICATE-/p'", 
                [
                    "ip" => $ip,
                    "port" => $port
                ]);

            $installed_cert = Command::run("cat {:path}{:name}", 
                [
                    "path" => "/usr/local/share/ca-certificates/",
                    "name" => "liman-" . strtolower($ip) . "_" . $port . ".crt"
                ]);

            return $srv_cert == $installed_cert;
        }
        return false;
    }
}

if (!function_exists('ldapCheck')) {
    function ldapCheck($domainname, $user, $pw, $ip, $port) 
    {
        restoreHandler();
        $username = $user."@".$domainname;
        $pass = $pw;
        $server = 'ldaps://'.$ip . ':' . $port;
        
        $ldap = ldap_connect($server);
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
        
        $bind = @ldap_bind($ldap, $username, $pass);

        setHandler();
        return $bind;
    }
}