<?php 
use Liman\Toolkit\Shell\Command;

    function index(){
        return view('index');
    }

    function verifyInstallation(){
        if(trim(runCommand('dpkg -s sambahvl | grep "Status" | grep -w "install" 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
            return respond(true,200);
        }else{
            return respond(false,200);
        }
    } 

    function verifyDomain(){
        $smbConfigPath = "/etc/samba/smb.conf";

        if(isFileExists($smbConfigPath) == true){
            return respond(true,200);
        }
        
        if(isFileExists($smbConfigPath) == false){
            return respond(false,200);
        }
<<<<<<< HEAD
        
        
=======
>>>>>>> f98e3bb0d8d48bb25d0604f1902a6e520b16e65f
    }

    function verifyInstallationPhp(){
        if(trim(runCommand('dpkg -s smbpy | grep "Status" | grep -w "install" 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
            return true;
        }else{
            return false;
        }

    }

    function installSmbPackage()
    {   
        $commandLine = "hostname";
        $hostname = runCommand(sudo() . $commandLine);
        //$newHostname =  extensionDb('machineName');

        $commandLine1 = "apt install gnupg2 ca-certificates -y";
        $commandLine2 = "echo 'deb [arch=amd64] http://depo.aciklab.org/ onyedi main' | sudo tee /etc/apt/sources.list.d/acikdepo.list";
        $commandLine3 = "wget --no-check-certificate -qO - http://depo.aciklab.org/public.key | sudo apt-key add -";
        $commandLine4 = "apt update";
        $commandLine5 = "bash -c 'DEBIAN_FRONTEND=noninteractive apt install sambahvl -qqy >/tmp/smbpyLog 2>&1 & disown'";

        runCommand(sudo() . $commandLine1);
        runCommand(sudo() . $commandLine2);
        runCommand(sudo() . $commandLine3);
        runCommand(sudo() . $commandLine4);
        runCommand(sudo() . $commandLine5);
        
        //$a = editHostsFile($hostname,$newHostname);  

        return respond($a,200);
    }

    function observeInstallation()
    {
        if(verifyInstallationPhp() == true){
            $res = "smbHVL paketi zaten var !";
            
            return respond($res, 202);
        }

        if(verifyInstallationPhp() == false){
            $log = runCommand(sudo() . 'cat /tmp/smbpyLog');
            
            return respond($log, 200);
        }
    }

    function createSambaDomain(){
        $domainName = extensionDb('domainName');
        $domainPassword = extensionDb('domainPassword');

        $createDomainCommand = "smb-create-domain -d " . $domainName . " -p " . $domainPassword;
        runCommand(sudo() . $createDomainCommand);
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

    function returnDomainInformations(){
        $domainName = extensionDb('domainName');
        $getDomainInformationCommand = "samba-tool domain info " . $domainName;

        $domainInformations = runCommand(sudo() . $getDomainInformationCommand);
        return respond($domainInformations,200);
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

    function isFileExists($filePath){
        $existsCommand = 'test -e '. $filePath .' && echo 1 || echo 0';
        $existsFlag = runCommand(sudo() . $existsCommand);

        if($existsFlag == 1){
            return true;
        }

        if($existsFlag == 0){
            return false;
        }

    }

    function tab2(){
        $output = runCommand(sudo() . "systemctl is-active samba4.service");

        if (trim($output) == "active") {
            return respond(true,200);
        } 
        else {
            return respond(false,200);
        }
    }

    function sambaLog(){
        $command = "systemctl status samba4.service";

        $output = runCommand(sudo() . $command);

        return respond($output, 200);
    }
<<<<<<< HEAD

    // FSMO-Role Management  == Tab 4 ==
    
    function printTable(){
        $allData = runCommand(sudo()."samba-tool fsmo show");
        $allDataList = explode("\n",$allData);
        $dict = [
            "SchemaMasterRole" => "schema",
            "InfrastructureMasterRole" => "infrastructure",
            "RidAllocationMasterRole" => "rid",
            "PdcEmulationMasterRole" => "pdc",
            "DomainNamingMasterRole" => "naming",
            "DomainDnsZonesMasterRole" => "domaindns",
            "ForestDnsZonesMasterRole" => "forestdns",
        ];
        $data = [];
        for($i=0; $i<count($allDataList); $i++){
            $item = $allDataList[$i];
            $itemList = explode(",",$item);

            $nameItem = explode("=",$itemList[1]);
            $nameItem = $nameItem[1];

            if ($nameItem != "") {
                $roleItem = explode(" ",$itemList[0]);
                $roleItem = $roleItem[0];
                $data[] = [
                    "role" => $roleItem,
                    "name" => $nameItem,
                    "contraction" => $dict[$roleItem]
                    
                ];  
            }
      
        }
        return view('table', [
            "value" => $data,
            "title" => ["Rol","Sunucu","*hidden*"],
            "display" => ["role","name","contraction:contraction"],
            "menu" => [

                "Bu rolü al" => [
                    "target" => "takeTheRole",
                    "icon" => "fa-file-export"
                ],
    
            ],
        ]);
    }

    function takeTheRole(){
        $contraction = request("contraction");
        $output=runCommand(sudo()."samba-tool fsmo transfer --role=$contraction -UAdministrator");
        return respond($output,200);
    }

    function takeAllRoles(){
        $output=runCommand(sudo()."samba-tool fsmo transfer --role=all -UAdministrator");
        return respond($output,200);
    }
=======
>>>>>>> f98e3bb0d8d48bb25d0604f1902a6e520b16e65f
?>
