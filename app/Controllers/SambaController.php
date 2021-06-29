<?php
namespace App\Controllers;

use Liman\Toolkit\Shell\Command;

class SambaController{
    
    //INSTALL
    function verifyInstallation(){
        if(trim(runCommand('dpkg -s sambahvl | grep "Status" | grep -w "install" 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
            return respond(true,200);
        }else{
            if(trim(runCommand('dpkg -s samba | grep "Status" | grep -w "install" 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
                return respond("Samba error !",202);
            }else{
                return respond(false,200);
            }
        }
    }

    function deleteSambaPackage(){
        $deletePackage = "apt -y autoremove samba*";
        runCommand(sudo() . $deletePackage);

        $deleteConfig = "rm -rf /etc/samba/smb.conf";
        runCommand(sudo() . $deleteConfig);

        if(trim(runCommand('dpkg -s samba | grep "Status" | grep -w "install" 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
            return respond(false,202);
        }else{
            return respond(true,200);
        }
    }

    function verifyInstallationPhp(){
        if(trim(runCommand('dpkg -s sambahvl | grep "Status" | grep -w "install" 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
            return true;
        }else{
            return false;
        }
    }

    public function installSmbPackage()
	{
		return respond(
			view('components.task', [
				'onFail' => 'onTaskFail',
				'onSuccess' => 'onTaskSuccess',
				'tasks' => [
					0 => [
						'name' => 'InstallDependencies',
						'attributes' => []
                    ],

                    1 => [
						'name' => 'InstallPackage',
						'attributes' => []
					]
				]
			]),
			200
		);
	}

    function observeInstallation(){
        $log = runCommand(sudo() . 'cat /tmp/domainLog');
        $check = "tail -n 1 /tmp/domainLog";
        if(runCommand(sudo() . $check)  == "Created symlink /etc/systemd/system/multi-user.target.wants/samba4.service → /etc/systemd/system/samba4.service."){
            if($this->checkDomainPhp() == true){
                return respond($log .= "\n\nKurulum başarıyla tamamlandı.", 202);
            }
            else{
                return respond("Domain kurulumu tamamlanamadı !",201);
            } 
        }
        return respond($log, 200);
    }

    function migrateLog(){
        $log = runCommand(sudo() . 'cat /tmp/migrateLog');
        $check = "tail -n 1 /tmp/migrateLog";

        $lastLine1 = "Created symlink /etc/systemd/system/multi-user.target.wants/samba4.service → /etc/systemd/system/samba4.service.";
        $lastLine2 = "smb-migrate-domain: servisler yeniden başlatılıyor";

        if(runCommand(sudo() . $check) == $lastLine1 or runCommand(sudo() . $check)  == $lastLine2){
            if($this->checkDomainPhp() == true){
                return respond($log .= "\n\nKurulum başarıyla tamamlandı.", 202);
            }
            else{
                return respond("Göç işlemi tamamlanamadı !",201);
            }
        }
        return respond($log, 200);
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

    function verifyDomain(){
        $smbConfigPath = "/etc/samba/smb.conf";

        if($this->isFileExists($smbConfigPath) == true){
            return respond(true,200);
        }
        
        if($this->isFileExists($smbConfigPath) == false){
            return respond(false,200);
        }
    }

    //CREATE DOMAIN
    function createSambaDomain(){
        $domainName = extensionDb('domainName');
        $domainPassword = extensionDb('domainPassword');
        $createDomainCommand ="bash -c 'DEBIAN_FRONTEND=noninteractive smb-create-domain -d " . $domainName . " -p " . $domainPassword . " > /tmp/domainLog 2>&1 & disown'";
        runCommand(sudo() . $createDomainCommand);
    }

    function returnDomainInformations(){
        $domainName = extensionDb('domainName');
        $getDomainInformationCommand = "samba-tool domain info " . $domainName;

        $domainInformations = runCommand(sudo() . $getDomainInformationCommand);
        return respond($domainInformations,200);
    }

    function returnSambaServiceStatus(){
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

    //FSMO
    function returnRolesTable(){
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
                    "icon" => "fa-share"
                ],
            ],
        ]);
    }

    function takeTheRole(){
        $contraction = request("contraction");
        $output=runCommand(sudo()."samba-tool fsmo transfer --role=".$contraction." -UAdministrator");
        if($output == ""){
            $output=runCommand(sudo()."samba-tool fsmo transfer --role=".$contraction." -UAdministrator 2>&1");
        }
        return respond($output,200);
    }

    function takeAllRoles(){
        $output=runCommand(sudo()."samba-tool fsmo transfer --role=all -UAdministrator");
        return respond($output,200);
    }

    function seizeTheRole(){
        $contraction = request("contraction");
        $output=runCommand(sudo()."samba-tool fsmo seize --role=".$contraction." -UAdministrator");
        return respond($output,200);
    }

    
    //REPLICATION
    function replicationOrganized(){
        $hostNameTo = runCommand("hostname");

        $allInfo = runCommand(sudo() . "samba-tool drs showrepl --json");
        $allInfo = json_decode($allInfo,true);

        $data = [];

        for ($i=0; $i < count($allInfo["repsFrom"]); $i++) {
            $pureHostName = str_replace("Default-First-Site-Name\\", "", $allInfo["repsFrom"][$i]["DSA"]);
            $data[] = [
                "hostNameTo" => $hostNameTo,
                "info" => $allInfo["repsFrom"][$i]["NC dn"],
                "hostNameFrom" => $pureHostName,
                "lastUpdateTime" => $allInfo["repsFrom"][$i]["last success"]
            ];
        }

        return view('table', [
            "value" => $data,
            "title" => ["Incoming Host Name", "Info", "Outgoing Host Name", "*hidden*"],
            "display" => ["hostNameTo", "info", "hostNameFrom", "lastUpdateTime:lastUpdateTime"],
            "onclick" => "test",
            "menu" => [

                "Update Replication" => [
                    "target" => "updateReplication",
                    "icon" => "fa-plus-circle"
                ],

                "Last Update Time" => [
                    "target" => "showUpdateTime",
                    "icon" => "fa-recycle"
                ],
            ],
        ]);   
    }

    function createBound(){ 
        $incomingHostName = request('inHost');
        $outgoingHostName = request('outHost');
        $info = request('info');
        runCommand(sudo() . 'samba-tool drs replicate ' . $incomingHostName . $outgoingHostName . $info);
        return respond("Islem basarili", 200);
    }

    function showUpdateTime(){
        $lastUpdateTime = request('lastUpdateTime');
        return respond($lastUpdateTime, 200);
        
    }

    //MIGRATION
    function migrateDomain(){

        validate([
			'ip' => 'required|string',
			'username' => 'required|string',
			'password' => 'required|string'
		]);
        
        $ip = request("ip");
        $username = request("username");
        $password = request("password");
        $migrateCommand = "bash -c 'DEBIAN_FRONTEND=noninteractive smb-migrate-domain -s " . $ip . " -a " . $username . " -p " . $password . " > /tmp/migrateLog 2>&1 & disown'";
        runCommand(sudo() . $migrateCommand);
    }

    function migrateSite(){

        validate([
			'ip' => 'required|string',
			'username' => 'required|string',
			'password' => 'required|string',
			'site' => 'required|string',

		]);

        $ip = request("ip");
        $username = request("username");
        $password = request("password");
        $site = request("site");

        $command = "bash -c 'DEBIAN_FRONTEND=noninteractive smb-migrate-domain -s " . $ip . " -a " . $username . " -p " . $password . " -t ". $site . " > /tmp/migrateLog 2>&1 & disown'";
        
        runCommand(sudo().$command);
        return respond("Success", 200);
    }

    //INFO
    function getSambaType(){
        if(trim(runCommand('dpkg -s sambahvl | grep "Status" | grep -w "install" 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
            return "sambahvl";
        }else{
            if(trim(runCommand('dpkg -s samba | grep "Status" | grep -w "install" 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
                return "samba";
            }
            return "not installed";
        }
    }
    
    function getSambaDetails(){
        $type = $this->getSambaType();
        
        if($type == "samba"){
            $output = runCommand(sudo() . "dpkg -s samba");

        }
        else if($type == "sambahvl"){
            $output = runCommand(sudo() . "dpkg -s sambahvl");

        }
        else{
            $output = "Samba is not installed.";
        }
        return respond($output,200);

    }

    function getSambaVersion(){

        $type = $this->getSambaType();

        if($type !== "not installed"){
            $version = runCommand(sudo() . "samba --version");
            return respond($version,200);
        }
        else{
            return respond("Samba is not installed.",200);

        }

    }

    function listPaths(){
        $command = "smbd -b | sed -n -e '/Paths:/,/System Headers:/ p' | head -n -2 | sed '1d;'";
        $output = runCommand(sudo().$command);
        $allDataList = explode("\n",$output);
        $data = [];
        for($i=0; $i<count($allDataList); $i++){
            $item = $allDataList[$i];
            $data[] = [
                "name" => $item,
            ];  
        }
        return view('table', [
            "value" => $data,
            "title" => ["Path"],
            "display" => ["name"],
        ]);

    }

    function listHave(){
        $command = "smbd -b | grep HAVE";
        $output = runCommand(sudo().$command);
        $allDataList = explode("\n",$output);
        $data = [];
        for($i=0; $i<count($allDataList); $i++){
            $item = $allDataList[$i];
            $data[] = [
                "name" => $item,
            ];  
        }
        return view('table', [
            "value" => $data,
            "title" => ["Have"],
            "display" => ["name"],
        ]);

    }
    function listBuildOptions(){
        $command = "smbd -b | sed -n -e '/Build Options:/,/Cluster support features:/ p' | head -n -2 | sed '1d;'";
        $output = runCommand(sudo().$command);
        $allDataList = explode("\n",$output);
        $data = [];
        for($i=0; $i<count($allDataList); $i++){
            $item = $allDataList[$i];
            $data[] = [
                "name" => $item,
            ];  
        }
        return view('table', [
            "value" => $data,
            "title" => ["Build option"],
            "display" => ["name"],
        ]);

    }

    function listWithOptions(){
        $command = "smbd -b | sed -n -e '/--with Options:/,/Build Options:/ p' | head -n -2 | sed '1d;'";
        $output = runCommand(sudo().$command);
        $allDataList = explode("\n",$output);
        $data = [];
        for($i=0; $i<count($allDataList); $i++){
            $item = $allDataList[$i];
            $data[] = [
                "name" => $item,
            ];  
        }
        return view('table', [
            "value" => $data,
            "title" => ["With option"],
            "display" => ["name"],
        ]);

    }

    function listModules(){
        $command = "smbd -b | grep 'Builtin modules:' -A1 | sed '1d;'";
        $output = runCommand(sudo().$command);
        $allDataList = explode(" ",$output);
        $data = [];
        for($i=0; $i<count($allDataList); $i++){
            $item = $allDataList[$i];
            $data[] = [
                "name" => $item,
            ];  
        }
        return view('table', [
            "value" => $data,
            "title" => ["Module"],
            "display" => ["name"],
        ]);

    }

    function getInstallLogs(){
        $flag1 = $this->isFileExists("/tmp/smbHvlLog.txt");
        if($flag1){
            $output = runCommand(sudo() . "cat /tmp/smbHvlLog.txt");
            return respond($output,200);
        }
        else{
            return respond("Bu eklenti ile bu makineye daha önce hiç sambahvl kurulmamış.",200);
        }

    }

    function getOtherLogs(){
        $flag1 = $this->isFileExists("/tmp/migrateLog");
        $flag2 = $this->isFileExists("/tmp/domainLog");
        if($flag1){
            $output = runCommand(sudo() . "cat /tmp/migrateLog");
            return respond($output,200);
        }
        if($flag2){
            $output = runCommand(sudo() . "cat /tmp/domainLog");
            return respond($output,200);
        }
        return respond("Bu eklenti ile bu makinede etki alanı oluşturma \nveya göç işlemi yapılmamış.",200);
        

    }

    function checkSambahvl(){
        if(trim(runCommand('dpkg -s sambahvl | grep "Status" | grep -w "install" 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
            return respond(true,200);
        }
        else{
            return respond(false,200);
        }
    }
    function checkDomain(){
        //if(trim(runCommand('net ads info | grep Realm: 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
        if(trim(runCommand('getent passwd Administrator| grep administrator 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
            return respond(true,200);
        }
        else{
            return respond(false,200);
        }
    }

    function checkDomainPhp(){
        if(trim(runCommand('getent passwd Administrator| grep administrator 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
            return true;
        }
        else{
            return false;
        }
    }
}
?>