<?php
namespace App\Controllers;

use Liman\Toolkit\Shell\Command;

class NewController{

    
    function showConfig(){

        $cmd = "smbd -b | grep CONFIGFILE";
        $output = runCommand(sudo().$cmd);
        $output = explode(" ", $output);

        $locOfFile = $output[1];
        $cmd = "cat ". $locOfFile;
        $output = runCommand(sudo().$cmd);

        return $output;
    }

    function getDNSForwarder(){

        $cmd = "smbd -b | grep CONFIGFILE";
        $locOfFile= runCommand(sudo().$cmd);
        $locOfFile = explode(" ", $locOfFile);
        $locOfFile =  $locOfFile[1];
      
        $cmd = "grep dns forwarder " . $locOfFile;
        $output = runCommand(sudo().$cmd);
        $output = explode(" ", $output, 5);
        $data = $output[4];
        return $data;
    }

    function changeDNSForwarder(){
        
        $cmd = "smbd -b | grep CONFIGFILE";
        $locOfFile= runCommand(sudo().$cmd);
        $locOfFile = explode(" ", $locOfFile);
        $locOfFile =  $locOfFile[1];
      
        $cmd1 = "grep 'dns forwarder' " . $locOfFile;
        $output = runCommand(sudo().$cmd1);
        $output = explode("= ", $output);
        $data = $output[1];

    
    //sed -i 's/8.8.8.8/178.233.140.110 46.197.15.60 176.240.150.250/gI' /etc/samba/smb.conf
        Command::runSudo("sed -i 's/@{:oldDNS}/@{:newDNS}/gI' @{:loc}",
        [
            "oldDNS" =>  $data,
            "newDNS" => request("dnsForwardData"),
            "loc" => $locOfFile
        ]);
    

        $reload = "systemctl restart samba4.service";
        runCommand(sudo().$reload);
        return respond($data);
    }


}

?>