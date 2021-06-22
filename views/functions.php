<?php 
use Liman\Toolkit\Shell\Command;

    function index(){
        return view('index');
    }


    function editHostsFile($hostname,$newHostname){
        $hostsFile = "/etc/hosts";
        $hostsLines = runCommand(sudo() . "cat " . $hostsFile);
        
        $newLine = "127.0.1.1\t". $newHostname;
        $newLine0 = "120.0.0.1\tlocalhost";

        $lineList = explode("\n",$hostsLines);
        unset($lineList[0]);
        unset($lineList[1]);
        array_unshift($lineList, $newLine);
        array_unshift($lineList, $newLine0);
        
        $lineString = implode("\n",$lineList);
        $commandLine1 = "hostnamectl set-hostname " . $newHostname;
        $commandLine2 = "hostnamectl " . $newHostname;

        $a = runCommand(sudo() . $commandLine1);
        $b = runCommand(sudo() . $commandLine2);

        $command = "sh -c 'echo " . '"' . $lineString . '"' . " > /etc/hosts'";
        runCommand(sudo() . $command);

        return $a;
    }

    

    function createSmbUser(){
        $userName = request('userName');
        $createUserCommand = "samba-tool user create " . $userName;
    }

    function checkHostname(){
        $commandLine = "hostname";
        $hostname = runCommand(sudo() . $commandLine);
        //$newHostname =  extensionDb('machineName');

        if($hostname == $newHostname){
            return respond(true,200);
        }
        else{
            return respond(false,202);
        }
    }

    // #### FSMO-Role Management Tab ####
    

    
?>
