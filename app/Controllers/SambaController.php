<?php
namespace App\Controllers;

use Liman\Toolkit\Shell\Command;

class SambaController{
    
    //INSTALL
    function verifyInstallation(){
        
        if(trim(Command::runSudo('dpkg -s sambahvl | grep "Status" | grep -w "install" 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
            return respond(true,200);
        }else{
            if(trim(Command::runSudo('dpkg -s samba | grep "Status" | grep -w "install" 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
                return respond("Samba error !",202);
            }else{
                return respond(false,200);
            }
        }
    }

    function checkInstallation(){

        $log = Command::runSudo('cat /tmp/smbHvlLog.txt');
        if(str_contains($log,"gpg: no valid OpenPGP data found.")){
            return respond(false,201);
        }
        else{
            return respond(true,200);

        }
    }
    //gpg: no valid OpenPGP data found.

    function deleteSambaPackage(){

        Command::runSudo("apt -y autoremove samba*");
        Command::runSudo("rm -rf /etc/samba/smb.conf");

        if(trim(Command::runSudo('dpkg -s samba | grep "Status" | grep -w "install" 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
            return respond(false,202);
        }
        else{
            return respond(true,200);
        }
    }

    function verifyInstallationPhp(){

        if(trim(Command::runSudo('dpkg -s sambahvl | grep "Status" | grep -w "install" 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
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

        $log = Command::runSudo('cat /tmp/domainLog');
        $flag = $this->checkDomainPhp();

        if($flag == true){
            return respond($log .= "\n\nKurulum başarıyla tamamlandı.", 202);
        }
        else{
            return respond($log, 200);

        }
    }

    function migrateLog(){

        $log = Command::runSudo('cat /tmp/migrateLog');
        $flag = $this->checkDomainPhp();

        if($flag == true){
            return respond($log .= "\n\nMigrate başarıyla tamamlandı.", 202);
        }
        else{
            return respond($log, 200);

        }
    }

    function isFileExists($filePath){

        $existsFlag = Command::runSudo('test -e @{:filePath} && echo 1 || echo 0', [
			'filePath' => $filePath
		]);

        if($existsFlag == 1){
            return true;
        }
        else{
            return false;
        }

    }

    function verifyDomain(){
        $smbConfigPath = "/etc/samba/smb.conf";
        $isFileExists = $this->isFileExists($smbConfigPath);
        if($isFileExists){
            return respond(true,200);
        }
        else{
            return respond(false,200);
        }
    }

    //CREATE DOMAIN
    function createSambaDomain(){

        Command::runSudo("bash -c 'DEBIAN_FRONTEND=noninteractive smb-create-domain -d @{:domainName} -p @{:domainPassword} -r @{:ip} > /tmp/domainLog 2>&1 & disown'", [
			'domainName' => extensionDb('domainName'),
			'domainPassword' => extensionDb('domainPassword'),
			'ip' => $this->getIP(),
		]);

    }

    function returnDomainInformations(){

        $domainInformations = Command::runSudo('samba-tool domain info @{:domainName}', [
			'domainName' => extensionDb('domainName')
		]);
        return respond($domainInformations,200);
    }

    function returnSambaServiceStatus(){
        if (trim(Command::runSudo("systemctl is-active samba4.service")) == "active") {
            return respond(true,200);
        } 
        else {
            return respond(false,200);
        }
    }

    function sambaLog(){

        return respond(Command::runSudo("systemctl status samba4.service"), 200);
    }

    //FSMO
    function returnRolesTable(){
        
        $allData = Command::runSudo("samba-tool fsmo show");
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
            "title" => ["Roller","Sunucular","*hidden*"],
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

        $output = Command::runSudo('samba-tool fsmo transfer --role=@{:contraction} -UAdministrator', [
			'contraction' => request('contraction')
		]);

        if($output == ""){
            $output = Command::runSudo('samba-tool fsmo transfer --role=@{:contraction} -UAdministrator 2>&1', [
                'contraction' => request('contraction')
            ]);
        }
        return respond($output,200);
    }

    function takeAllRoles(){

        $output = Command::runSudo('samba-tool fsmo transfer --role=all -UAdministrator');
        return respond($output,200);
    }

    function seizeTheRole(){

        $output = Command::runSudo('samba-tool fsmo seize --role=@{:contraction} -UAdministrator', [
			'contraction' => request('contraction')
		]);
        return respond($output,200);
    }

    
    //REPLICATION
    function replicationOrganized(){
        $hostNameTo = Command::run("hostname");

        $allInfo = Command::runSudo("samba-tool drs showrepl --json");
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
            "title" => ["Gelen Ana Bilgisayar Adı", "Bilgi", "Giden Ana Bilgisayar Adı", "*hidden*"],
            "display" => ["hostNameTo", "info", "hostNameFrom", "lastUpdateTime:lastUpdateTime"],
            "onclick" => "test",
            "menu" => [

                "Replikasyonu Güncelle" => [
                    "target" => "updateReplication",
                    "icon" => "fa-plus-circle"
                ],

                "Son Güncelleme Zamanı" => [
                    "target" => "showUpdateTime",
                    "icon" => "fa-recycle"
                ],
            ],
        ]);   
    }

    function createBound(){ 

        Command::runSudo('samba-tool drs replicate @{:incomingHostName} @{:outgoingHostName} @{:info}', [
			'incomingHostName' => request('inHost'),
			'outgoingHostName' => request('outHost'),
			'info' => request('info')

		]);
        return respond("Başarılı", 200);
    }

    //MIGRATION
    function migrateDomain(){

        validate([
			'ip' => 'required|string',
			'username' => 'required|string',
			'password' => 'required|string'
		]);
        
        Command::runSudo("bash -c 'DEBIAN_FRONTEND=noninteractive smb-migrate-domain -s @{:ip} -a @{:username} -p @{:password} > /tmp/migrateLog 2>&1 & disown'", [
			'ip' => request('ip'),
			'username' => request('username'),
			'password' => request('password')

		]);

        $this->timeupdate();
    }

    function migrateSite(){

        validate([
			'ip' => 'required|string',
			'username' => 'required|string',
			'password' => 'required|string',
			'site' => 'required|string',

		]);
        //$migrateCommand = "bash -c 'DEBIAN_FRONTEND=noninteractive smb-migrate-domain -s \"" . $ip . "\" -a \"" . $username . "\" -p \"" . $password . "\" -t \"". $site . "\" > /tmp/migrateLog 2>&1 & disown'";

        putFile(getPath("scripts/smb_migrate_domain"), "/tmp/smb_migrate_domain");
        Command::runSudo("chmod +x /tmp/smb_migrate_domain");
        Command::runSudo("cp /tmp/smb_migrate_domain /usr/local/bin/smb-migrate-domain");

        Command::runSudo("bash -c 'DEBIAN_FRONTEND=noninteractive smb-migrate-domain -s @{:ip} -a @{:username} -p @{:password} -t @{:site} > /tmp/migrateLog 2>&1 & disown'", [
			'ip' => request('ip'),
			'username' => request('username'),
			'password' => request('password'),
			'site' => request('site')

		]);

        $this->timeupdate();
    }

    //INFO
    function getSambaType(){
        
        if(trim(Command::runSudo('dpkg -s sambahvl | grep "Status" | grep -w "install" 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
            return "sambahvl";
        }else{
            if(trim(Command::runSudo('dpkg -s samba | grep "Status" | grep -w "install" 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
                return "samba";
            }
            return "not installed";
        }
    }
    
    function getSambaDetails(){
        $type = $this->getSambaType();
        
        if($type == "samba"){
            $output = Command::runSudo("dpkg -s samba");
        }
        else if($type == "sambahvl"){
            $output = Command::runSudo("dpkg -s sambahvl");
        }
        else{
            $output = "";
        }
        return respond($output,200);

    }

    function getSambaHvlVersion(){
        $type = $this->getSambaType();
        
        if($type == "sambahvl"){
            $output = Command::runSudo("dpkg -s sambahvl | grep Version");
            $output = explode(" ", $output);
            $output = $output[1];
        }
        else{
            $output = "- ";
        }
        return respond($output,200);

    }

    function getSambaVersion(){

        $type = $this->getSambaType();

        if($type !== "not installed"){
            $version = Command::runSudo("samba --version");
            $version = explode(" ", $version);
            return respond($version[1], 200);
        }
        else{
            return respond("",200);

        }

    }

    function listPaths(){

        $output = Command::runSudo("smbd -b | sed -n -e '/Paths:/,/System Headers:/ p' | head -n -2 | sed '1d;'");
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

        $output = Command::runSudo("smbd -b | grep HAVE");
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
        
        $output = Command::runSudo("smbd -b | sed -n -e '/Build Options:/,/Cluster support features:/ p' | head -n -2 | sed '1d;'");
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

        $output = Command::runSudo("smbd -b | sed -n -e '/--with Options:/,/Build Options:/ p' | head -n -2 | sed '1d;'");
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

        $output = Command::runSudo("smbd -b | grep 'Builtin modules:' -A1 | sed '1d;'");
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
            return respond(Command::runSudo("cat /tmp/smbHvlLog.txt"),200);
        }
        else{
            return respond("",200);
        }

    }

    function getOtherLogs(){
        $flag1 = $this->isFileExists("/tmp/migrateLog");
        $flag2 = $this->isFileExists("/tmp/domainLog");
        if($flag1){
            return respond(Command::runSudo("cat /tmp/migrateLog"),200);
        }
        if($flag2){
            return respond(Command::runSudo("cat /tmp/domainLog"),200);
        }
        return respond("",200);
        

    }

    function checkSambahvl(){
        
        if(trim(Command::runSudo('dpkg -s sambahvl | grep "Status" | grep -w "install" 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
            return respond(true,200);
        }
        else{
            return respond(false,200);
        }
    }
    function checkDomain(){
        
        if(trim(Command::runSudo('getent passwd Administrator| grep administrator 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
            return respond(true,200);
        }
        else{
            return respond(false,200);
        }
    }

    function checkDomainPhp(){
        
        if(trim(Command::runSudo('getent passwd Administrator| grep administrator 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
            return true;
        }
        else{
            return false;
        }
    }

    function timeUpdate(){
        
        $output = Command::runSudo("ntpdate -u tr.pool.ntp.org");
        $output = explode("ntp", $output)[0];
        return respond($output, 200);
    }

    function demoteThisOne(){

        $serverName = request("serverName");
        $fsmoResult = Command::runSudo("samba-tool fsmo show");
        
        if(stripos($fsmoResult, $serverName) != false){
            return respond("Bu Domain Controller üzerinde hala FSMO rolü bulunmaktadır. Lütfen FSMO Rol Yönetimi sekmesinden üzerindeki rolleri alıp tekrar demote ediniz!", 201);
        }

        $log = Command::runSudo("samba-tool domain demote --remove-other-dead-server=@{:serverName}", [
			'serverName' => $serverName
		]);

        if(str_contains($log, "ERROR")){
            return respond("Hata!", 201);
        }
        return respond("Basarili!", 200);
    }

    function checkForUpdates(){
        
        $output = Command::runSudo("apt list --upgradable | grep -q sambahvl && echo true || echo false");
        $type = $this->getSambaType();
        if($type != "not installed"){
            if(str_contains($output,"true")){
                return respond("upgradable",200);
            }
            else{
                return respond("not upgradable",200);
            }
        }
        else{
            return respond("not installed",200);
        }
        
    }

    function checkForUpdatesPhp(){

        $output = Command::runSudo("apt list --upgradable | grep -q sambahvl && echo true || echo false");

        if(str_contains($output,"true")){
            return true;
        }
        else{
            return false;
        }
      
    }

    function updateSambaPackage(){

        Command::runSudo("bash -c 'DEBIAN_FRONTEND=noninteractive apt-get install sambahvl > /tmp/updateLog 2>&1 & disown'");
    }

    function observeUpdate(){
        
        $log = Command::runSudo('cat /tmp/updateLog');
        $flag = $this->checkForUpdatesPhp();

        if($flag == false){
            return respond($log .= "\n\nGüncelleme başarıyla tamamlandı.", 202);
        }
        else{
            return respond($log, 200);

        }
    }
    function showConfig(){  //return config file content

        
        $output = explode(" ", Command::runSudo("smbd -b | grep CONFIGFILE"));

        $cmd_output = Command::runSudo("cat @{:locOfFile}", [
			'locOfFile' => $output[1]
		]);

        return $cmd_output;
    }

    function getDNSForwarder(){

        $locOfFile= Command::runSudo("smbd -b | grep CONFIGFILE");
        $locOfFile = explode(" ", $locOfFile);
        $locOfFile =  $locOfFile[1];

        $output = Command::runSudo("grep dns forwarder  @{:locOfFile}", [
			'locOfFile' => $locOfFile
		]);
        $output = explode(" ", $output, 5);
        $dnsForwarderData = $output[4];
        return $dnsForwarderData;
    }

    function changeDNSForwarder(){
        
        $locOfFile= Command::runSudo("smbd -b | grep CONFIGFILE");
        $locOfFile = explode(" ", $locOfFile);
        $locOfFile =  $locOfFile[1];
      
      
        $output = Command::runSudo("grep dns forwarder  @{:locOfFile}", [
			'locOfFile' => $locOfFile
		]);
        $output = explode("= ", $output);
        $dnsForwarderData = $output[1];
    
       //sed -i 's/oldData/newData/gI' <filepath>
        Command::runSudo("sed -i 's/'@{:oldDNS}'/'@{:newDNS}'/gI' @{:loc}",
        [
            "oldDNS" =>  $dnsForwarderData,
            "newDNS" => request("dnsForwardData"),
            "loc" => $locOfFile
        ]);
        
        $reload = "systemctl restart samba4.service";

        return respond(request("dnsForwardData")); 
    }

    function DnsUpdate(){
        return respond(Command::runSudo("samba_dnsupdate --verbose 2>&1"));
    }

    function getIP(){
        return Command::runSudo("hostname -I | awk '{print $1}'");
    }

}
?>