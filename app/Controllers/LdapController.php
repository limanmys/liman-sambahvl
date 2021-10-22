<?php
namespace App\Controllers;

use Liman\Toolkit\Formatter;
use Liman\Toolkit\Shell\Command;

class LdapController extends LdapConnection
{
    private $basedn = "";
    private $demote = 0;
    private $theIP = "";

	function __construct(){

        $IP = ($this->demote == 0) ? $this->getIP() : $this->theIP;
        $USERNAME = "administrator";
        $PASSWORD = extensionDb('domainPassword');
        $SSL = true;
        $BASE_DN = $this->getBaseDN(strtolower(extensionDb('domainName')));
        $PORT=636;

        parent::__construct($IP, $USERNAME, $PASSWORD, $SSL, $BASE_DN, $PORT);

        $this->basedn = $BASE_DN;
        $this->demote = 0;
    }
    
    function getBaseDN($domainname){
        $BASE_DN = "";
        $str = explode(".",$domainname);
        for($i=0 ; $i<count($str) ; $i++){
            if($str[$i] == end($str)){
                $BASE_DN .= "DC=".$str[$i];
            }
            else{
                $BASE_DN .= "DC=".$str[$i].",";
            }
        }
        return $BASE_DN;
    }

    function getIP(){
        $command = "hostname -I | awk '{print $1}'";
        $ip = runCommand(sudo().$command);
        return $ip;
    }

    function createUser(){

        validate([
			'username' => 'required|string',
			'password' => 'required|string'

		]);

        $username = request("username");
        $password = request("password");
        $output = runCommand(sudo()."samba-tool user create ".$username." ".$password." 2>&1");
        if(str_contains($output,"already exists")){
            return respond("Kullanıcı zaten mevcut !",201);
        }
        else if(str_contains($output,"password is too short")){
            return respond("Şifre çok kısa !",201);
        }
        else if (str_contains($output,"created successfully")){
            return respond("Kullanıcı başarıyla oluşturuldu.",200);
        }
        else if (str_contains($output,"not meet the complexity criteria!")){
            return respond("Şifre yeterince kompleks değil !",201);
        }
        else{
            return respond($output,201);
        }
    }

    function listUsers(){
        $ldap= parent::getConnection();
        $filter = "(&(objectClass=user)(objectCategory=person))";
        $result = ldap_search($ldap, $this->basedn, $filter);
        $entries = ldap_get_entries($ldap,$result);

        $count = ldap_count_entries($ldap, $result);
        $data = [];
        for($i=0 ; $i<$count ; $i++){
            $nameItem = $entries[$i]["name"][0];
            $samAcName = $entries[$i]["samaccountname"][0];
            $data[] = [
                "name" => $nameItem,
                "samaccountname" => $samAcName
            ];
        }

        return view('table', [
            "value" => $data,
            "title" => ["Kullanıcılar", "*hidden*"],
            "display" => ["name","samaccountname:samaccountname"],
            "onclick" => "showAttributes",
            "menu" => [

                "Sil" => [
                    "target" => "deleteUser",  
                    "icon" => "fa-trash-alt",             
                ],
            ]
    
        ]);

    }

    function getAttributes(){

        $ldap= parent::getConnection();
        $samacname = request("samaccountname");
        $filter = "CN=".$samacname;
        $search = ldap_search($ldap, $this->basedn, $filter);
        $info = ldap_get_entries($ldap, $search);
        $attributes = [];
        $attrSize = $info[0]["count"];


        
        for ($i = 0; $i < $attrSize; $i++)
        {
            $key = $info[0][$i];
            $count = $info[0][$key]["count"];  
            if($count > 1)
            {
                array_splice($info[0][$key],0,1);
                $info[0][$key][0] = implode(",",$info[0][$key]); 
            }
          

            $value = $info[0][$key][0];
            array_push($attributes, 
            [	
                "key" => $key,
                "value" => $value,
            ]);
        }

        return view('table', [
            "value" => $attributes,
            "title" => ["Nitelik", "Değer"],
            "onclick" => "showAttributeUpdateModal",
            "display" => ["key", "value"]
        ]);
    }

    function updateAttribute(){

        $ldap= parent::getConnection();
        $samacname = request("samaccountname");
        $filter = "CN=".$samacname;
        $search = ldap_search($ldap, $this->basedn, $filter);
        $info = ldap_get_entries($ldap, $search);
        $attrSize = $info[0]["count"];

        $update_key = request("key");
        $update_value = request("value");



        if($attrSize > 0){
            $old_value =$info[0][$update_key][0];


            if($old_value != NULL){
                if($old_value == $update_value)
                    return respond('Deger Degismedi', 201);

                $values = explode(',', $update_value);
                
                $res = ldap_mod_replace($ldap, $info[0]['dn'], [$update_key => $values[0]]);


                return respond($res, 200);
            }
            else{
                return respond('ERROR', 404);
            }
        }

        return respond('OK', 200);
    }

    function deleteUser(){
        $user = request("name");
        $output=runCommand(sudo()."smbpasswd -x ". $user);
        return respond($output,200);
    }

    function addUserToGroup(){
        $group = request("group");


        $ldap= parent::getConnection();
        $user = request("user");
        $filter = "CN=".$user;
        $search = ldap_search($ldap, $this->basedn, $filter);
        $userInfo = ldap_get_entries($ldap, $search);

        $search = ldap_search($ldap, $this->basedn, $group);
        $groupInfo = ldap_get_entries($ldap, $search);


        if(count($userInfo) == 0 || count($groupInfo) == 0)
            return respond("NOT FOUND", 404);
        
            

        $res = ldap_mod_add($ldap, $group, ["member" => $userInfo[0]["dn"]]);
        return respond($res, 200);
    }

    function createGroup(){

        validate([
            'groupname' => 'required|string',
    
        ]);
    
        $groupname = request("groupname");
        $output = runCommand(sudo()."samba-tool group add ".$groupname." 2>&1");
        if(str_contains($output,"already exists")){
            return respond("Grup zaten mevcut !",201);
        }
        else if(str_contains($output,"Added")){
            return respond("Grup başarıyla oluşturuldu.",200);
        }
        else{
            return respond($output,201);
    
        }
    }
    
    function listGroups(){
        $ldap= parent::getConnection();
        $groupType = request("groupType");
        if($groupType == "none")
            $filter="objectClass=group";
        else if($groupType == "security")
            $filter = "(&(objectCategory=group)(groupType:1.2.840.113556.1.4.803:=2147483648))";
        else if($groupType == "distribution")
            $filter = "(&(objectCategory=group)(!(groupType:1.2.840.113556.1.4.803:=2147483648)))";
        
        $result = ldap_search($ldap, $this->basedn, $filter);
        $entries = ldap_get_entries($ldap,$result);
    
        $count = ldap_count_entries($ldap, $result);
        $data = [];
        for($i=0 ; $i<$count ; $i++){
            $nameItem = $entries[$i]["name"][0];
            $dn = $entries[$i]["dn"];
            $data[] = [
                "name" => $nameItem,
                "dn" => $dn
            ];
        }
    
        return view('table', [
            "value" => $data,
            "title" => ["Gruplar", "*hidden*"],
            "display" => ["name", "dn:dn"],
            "onclick" =>"showGroupMembers",
            "menu" => [
                "Sil" => [
                    "target" => "deleteGroup", 
                    "icon" => "fa-trash-alt",              
                ],
                ]
        ]);
    
    }

    function deleteGroup(){
        $group = request("name");
        $output=runCommand(sudo()."samba-tool group delete " . $group);
        return respond($output,200);
    }

    function getGroupMembers(){
        $ldap= parent::getConnection();
        $groupDN= request("groupDN");
        $filter = "memberOf=" . $groupDN;
        $result = ldap_search($ldap, $this->basedn, $filter, ["name"]);
        $entries = ldap_get_entries($ldap,$result);
    
       
        $count = $entries["count"];
        $data = [];
        for($i=0 ; $i<$count ; $i++){
            $name = $entries[$i]["name"][0];
            //$dn = $entries[$i]["dn"];
            $data[] = [
                "name" => $name,
            ];
        }
    
        return view('table', [
            "value" => $data,
            "title" => ["Üyeler"],
            "display" => ["name"]
        ]);

        return respond($entries,200);
    }

    function listComputers(){
        $ldap= parent::getConnection();
    
        $filter = "objectClass=computer";
        $result = ldap_search($ldap, $this->basedn, $filter);
        $entries = ldap_get_entries($ldap,$result);
    
        $count = ldap_count_entries($ldap, $result);
        $data = [];
        for($i=0 ; $i<$count ; $i++){
            $nameItem = $entries[$i]["name"][0];
            $data[] = [
                "name" => $nameItem
            ];
        }
    
        return view('table', [
            "value" => $data,
            "title" => ["Bilgisayarlar"],
            "display" => ["name"],
            "menu" => [
                "Sil" => [
                    "target" => "deleteComputer",  
                    "icon" => "fa-trash-alt",             
                ],
            ]
        ]);
    }

    function createComputer(){

        $output = Command::runSudo("samba-tool computer create @{:computerName} 2>&1",
        [
            "computerName" => request("computerName")
        ]);

        if(str_contains($output,"already exists")){
            return respond("Bilgisayar zaten mevcut !",201);
        }
        else if(str_contains($output,"created")){
            return respond("Bilgisayar başarıyla oluşturuldu.",200);
        }
        else{
            return respond($output,201);
        }
    }

    function deleteComputer(){
        $output = Command::runSudo("samba-tool computer delete @{:computerName}",
        [
            "computerName" => request("computerName")
        ]);
        return respond($output,200);
    }

    //ORGANIZATIONS

    function listOrganizations(){


        $ldap= parent::getConnection();
        $filePath = request('path');    // staj.lab
        if($filePath == strtolower(extensionDb('domainName'))){
            $baseDN = $this->basedn;
        }
        
        else{  
            $baseDN = $filePath;     //ou=tr,dc=staj,dc=lab
        }

        $filter = "objectClass=container";
        #$justthese = ["ou"];
        $list = ldap_list($ldap, $baseDN, $filter);
        $info = ldap_get_entries($ldap, $list);
  
        
        $data = [];
   
        for($i=0; $i<$info["count"]; $i++){
           $dn= $info[$i]["dn"];         //OU=ankara,OU=TR,DC=staj,DC=lab
           $pos = stripos($dn,',');
           $parentdn = substr($dn,$pos+1); //OU=TR,DC=staj,DC=lab
           $item_parent[$dn] =  $parentdn;      
        }

        	
       if(empty($item_parent)) {return;}

       foreach($item_parent as $key=>$value){
            $pid = $value;
            $id = $key; 

            $str = explode(",",$key);        //OU=ankara,OU=TR,DC=staj,DC=lab
            $str = explode("=",$str[0]);    // OU=ankara
            $name = $str[1];                // ankara
    
            array_push($data, [
                "id" => $id,
                "parent" => $pid,
                "text" => $name,
                "type" => "folder"
            ]);
        }
        return respond($data,200);
       
    }

    function listObjects(){

        $ldap= parent::getConnection();
        $theBaseDN = request('path'); 
        $filter = "(!(dn=".$theBaseDN."))";
        $result = ldap_search($ldap, $theBaseDN, $filter);
        $entries = ldap_get_entries($ldap,$result);
        $count = ldap_count_entries($ldap, $result);

        if($count == 0){
            return view('table', []);
        }

        $data = [];
        
        for($i=0 ; $i<$count ; $i++){
            $nameItem = $entries[$i]["name"][0];
            $data[] = [
                "name" => $nameItem
            ];
        }
        return view('table', [
            "value" => $data,
            "title" => ["Objeler"],
            "display" => ["name"],
        ]);
    }

    //Site
    function listSites(){

        $ldap= parent::getConnection();

        $filter = "objectClass=site";
        $result = ldap_search($ldap, "CN=Configuration,".$this->basedn, $filter);
        $entries = ldap_get_entries($ldap,$result);

        $count = ldap_count_entries($ldap, $result);
        $data = [];
        for($i=0 ; $i<$count ; $i++){
            $nameItem = $entries[$i]["name"][0];
            $data[] = [
                "name" => $nameItem,
            ];
        }

        return view('table', [
            "value" => $data,
            "title" => ["Sitelar"],
            "display" => ["name"],
            "onclick" => "openSite",
            "menu" => [
                "Site Sil" => [
                    "target" => "deleteSite",
                    "icon" => "fas fa-trash-alt",
                ],  
                "Sunucular" => [
                    "target" => "showServersOfSite",
                    "icon" => "fas fa-server",
                ],  
                "Sunucu Ekle" => [
                    "target" => "addServerToSite",
                    "icon" => "fas fa-plus",
                ]
            ],
        ]);
    }

    function createSite(){

        validate([
			'newSiteName' => 'required|string',
		]);

        $newSiteName = request("newSiteName");
        $command = "samba-tool sites create ".$newSiteName;
        $commandOutput = runCommand(sudo().$command);
        return respond($commandOutput, 200);
    }

    function deleteSite(){

        $siteName = request("siteName");
        $command = "samba-tool sites remove ".$siteName;
        $commandOutput = runCommand(sudo().$command);
        return respond($commandOutput, 200);
    }

    function serversOfSite(){

        $siteName = request("siteName");

        $ldap= parent::getConnection();
        $filter = "objectClass=server";

        $result = ldap_search($ldap, "CN=Configuration,".$this->basedn, $filter);
        $entries = ldap_get_entries($ldap,$result);
        $count = ldap_count_entries($ldap, $result);
        $data = [];
        for($i=0 ; $i<$count ; $i++){
            if(str_contains($entries[$i]["distinguishedname"][0], $siteName)){
                $nameItem = $entries[$i]["name"][0];
                $data[] = [
                    "name" => $nameItem,
                ];
            }       
        }

        return view('table', [
            "value" => $data,
            "title" => ["Sunucular"],
            "display" => ["name"],
        ]);
    }

    function addServerToSite(){

        $newSiteName = request("newSiteName");
        $ldap= parent::getConnection();
        $filter = "objectClass=server";
        $result = ldap_search($ldap, "CN=Configuration,".$this->basedn, $filter);
        $entries = ldap_get_entries($ldap,$result);
        $count = ldap_count_entries($ldap, $result);
        $data = [];
        for($i=0 ; $i<$count ; $i++){
            if(!(str_contains($entries[$i]["distinguishedname"][0], $newSiteName))){
                $nameItem = $entries[$i]["name"][0];
                $dnOfServer = $entries[$i]["dn"];
                $data[] = [
                    "name" => $nameItem,
                    "dnOfServer" => $dnOfServer,
                    "newSiteName" => $newSiteName
                ];
            }       
        }

        return view('table', [
            "value" => $data,
            "title" => ["Mevcut Sunucular", "*hidden*", "*hidden*"],
            "display" => ["name", "dnOfServer:dnOfServer", "newSiteName:newSiteName"],
            "onclick" => "addThisServer"
        ]);
    }

    function addThisServer(){

        $dnOfServer = request("dnOfServer");
        $newSiteName = request("newSiteName");
        $ldap= parent::getConnection();

        $newRDN = substr($dnOfServer, 0, strpos($dnOfServer, ","));
        $newParent = "CN=Servers,CN=".$newSiteName.",CN=Sites,CN=Configuration,".$this->basedn;

        if(ldap_rename($ldap, $dnOfServer, $newRDN, $newParent, true)){
            return respond("Success!",200);
        }
        else {
            return respond("Error!",201);
        }
    }

    function ldapLogin(){

        validate([
			'ip' => 'required|string',
			'username' => 'required|string',
			'password' => 'required|string'

		]);

        $ip = request("ip");
        $username = request("username");
        $pass = request("password");
        $domainname= strtolower($this->getDomainNameAnonymously($ip));
        $user ="administrator@" . $domainname;
        $server = 'ldaps://'.$ip;
        $port="636";
        
        $str = explode(".",$domainname);
        $basedn = "DC=".$str[0].",DC=".$str[1];

        $ldap = ldap_connect($server);
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
        $bind=ldap_bind($ldap, $user, $pass);
        if (!$bind) {
            exit('Binding failed');
        }

        $filter = "objectClass=site";
        $result = ldap_search($ldap, "CN=Configuration,".$basedn, $filter);
        $entries = ldap_get_entries($ldap,$result);
        $count = ldap_count_entries($ldap, $result);

        for($i=0 ; $i<$count ; $i++){
            $nameItem[] = $entries[$i]["name"][0];
        }

        
        return respond($nameItem,200);
    }

    function getDomainNameAnonymously($server){
        $ldapconn = ldap_connect($server);
        ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

        $filter = "objectClass=*";
        if ($ldapconn) {
            // binding anonymously
            $ldapbind = ldap_bind($ldapconn);

            if ($ldapbind) {
                $result = ldap_read($ldapconn, '', '(objectclass=*)', array('namingContexts'));
                $data = ldap_get_entries($ldapconn, $result);

                $baseDN=$data[0]['namingcontexts'][0];
                $base=explode(",",$baseDN);
                $domainName = "";
                for($i = 0; $i < count($base); $i++){
                    if($base[$i] == end($base)){
                        $domainName .= str_replace("DC=","",$base[$i]);
                    }else{
                        $domainName .= str_replace("DC=","",$base[$i]) . ".";
                    }
                }

            } else {
                echo "LDAP bind anonymous failed...";
            }

        }
        return $domainName;
    }
    
    function listDemotable(){

        $hostNameOfThis = runCommand(sudo()."hostname");
        $ldap= parent::getConnection();
        $filter = "objectClass=computer";
        $binddn = $this->basedn;

        $result = ldap_search($ldap, "OU=Domain Controllers,".$binddn, $filter);
        $entries = ldap_get_entries($ldap,$result);
        $count = ldap_count_entries($ldap, $result);

        $data = [];
        for($i = 0; $i < $count; $i++){
            
            $thisComputer = $entries[$i]["name"][0];
            if(strcasecmp($thisComputer, $hostNameOfThis) != 0){

                $data[] = [
                    "serverName" => $thisComputer,
                ];
            }
        }   

        return view('table', [
            "value" => $data,
            "title" => ["Düşürülebilir Etki Alanı Denetleyicisi Adı"],
            "display" => ["serverName"],
            "menu" => [
                "Demote" => [
                    "target" => "demoteThisOne",
                    "icon" => "fas fa-unlink"
                ]
            ]
        ]); 
    }

    function recursiveLdapDelete($ldap, $dn){

        $result = ldap_list($ldap, $dn, "ObjectClass=*", array(""));
        $entries = ldap_get_entries($ldap, $result);
        $count = ldap_count_entries($ldap, $result);
        for($i=0; $i<$count; $i++){

            $this->recursiveLdapDelete($ldap, $entries[$i]['dn']);
        }
        ldap_delete($ldap, $dn);
        return;
    }

    function demoteYourself(){

        $serverName = runCommand(sudo()."hostname");
        $fsmoResult = runCommand(sudo()."samba-tool fsmo show");
        if(stripos($fsmoResult, $serverName) != false){
            return respond("Bu Domain Controller üzerinde hala FSMO rolü bulunmaktadır. Lütfen FSMO Rol Yönetimi sekmesinden üzerindeki rolleri alıp tekrar demote ediniz!", 201);
        }

        $netAdsOutput = runCommand(sudo()."net ads info");
        $netAdsOutput = explode("\n", $netAdsOutput);
        $secondLine = $netAdsOutput[1];
        $fullDomainName = substr(explode(":", $secondLine)[1],1);
        $primaryDcName = strtoupper(explode(".", $fullDomainName)[0]);
        
        runCommand(sudo()."samba-tool domain demote -Uadministrator --server=".$primaryDcName." --password ".extensionDb('domainPassword'). " > /tmp/demote-yourself-log.txt 2>&1");
        $outputOfDemote = runCommand(sudo()."cat /tmp/demote-yourself-log.txt");
        if(stripos($outputOfDemote, "Demote Successful") != false){

            $firstLine = $netAdsOutput[0];
            $this->theIP = substr(explode(":", $firstLine)[1],1);
            $this->demote = 1;

            $ldap= parent::getConnection();
            $filter = "objectClass=server";
            $result = ldap_search($ldap, "CN=Configuration,".$this->basedn, $filter);
            $entries = ldap_get_entries($ldap,$result);
            $count = ldap_count_entries($ldap, $result);
            $dn;
            for($i=0 ; $i<$count ; $i++){
                
                $nameItem = $entries[$i]["name"][0];
                if(strcasecmp($nameItem, $serverName) == 0){
                    $dn = $entries[$i]["distinguishedname"][0];
                }
            }
            $this->recursiveLdapDelete($ldap, $dn);
            $dn = "CN=".$serverName.",CN=Computers,".$this->basedn;
            $this->recursiveLdapDelete($ldap, $dn);

            $smbdOutput = runCommand(sudo()."smbd -b | grep PRIVATE");
            $privateDir = explode(":", $smbdOutput)[1];
            $privateDir = trim($privateDir);
            runCommand(sudo()."rm -rf ".$privateDir);
            runCommand(sudo()."mkdir ".$privateDir);

            $dhcpOutput = runCommand(sudo()."smb-dhcp-client");
            $dhcpOutput = explode("\n",$dhcpOutput);
            $lastLine = $dhcpOutput[count($dhcpOutput)-1];
            $dnsAddresses = substr(explode(":", $lastLine)[1],1);
            $dnsAddresses = explode(" ", $dnsAddresses);
            runCommand(sudo()."chattr -i /etc/resolv.conf");
            for($i = 0; $i < count($dnsAddresses); $i++){

                $willBeWritten = "nameserver ".$dnsAddresses[$i];
                runCommand("echo ".$willBeWritten." >> /tmp/new_dns_addresses.txt");
            }
            runCommand(sudo()."cp /tmp/new_dns_addresses.txt /etc/resolv.conf");

            runCommand(sudo()."rm /etc/samba/smb.conf");
            runCommand(sudo()."systemctl stop samba4.service");
            return respond("Başarılı", 200);
        }
        
        return respond($outputOfDemote, 201);
    }

    function onlyConfigureDocuments(){
        
        $serverName = runCommand(sudo()."hostname");
        $netAdsOutput = runCommand(sudo()."net ads info");
        $firstLine = $netAdsOutput[0];
        $this->theIP = substr(explode(":", $firstLine)[1],1);
        $this->demote = 1;

        $ldap= parent::getConnection();
        $filter = "objectClass=server";
        $result = ldap_search($ldap, "CN=Configuration,".$this->basedn, $filter);
        $entries = ldap_get_entries($ldap,$result);
        $count = ldap_count_entries($ldap, $result);
        $dn;
        for($i=0 ; $i<$count ; $i++){
            
            $nameItem = $entries[$i]["name"][0];
            if(strcasecmp($nameItem, $serverName) == 0){
                $dn = $entries[$i]["distinguishedname"][0];
            }
        }
        $this->recursiveLdapDelete($ldap, $dn);
        $dn = "CN=".$serverName.",CN=Computers,".$this->basedn;
        $this->recursiveLdapDelete($ldap, $dn);

        $smbdOutput = runCommand(sudo()."smbd -b | grep PRIVATE");
        $privateDir = explode(":", $smbdOutput)[1];
        $privateDir = trim($privateDir);
        runCommand(sudo()."rm -rf ".$privateDir);
        runCommand(sudo()."mkdir ".$privateDir);

        $dhcpOutput = runCommand(sudo()."smb-dhcp-client");
        $dhcpOutput = explode("\n",$dhcpOutput);
        $lastLine = $dhcpOutput[count($dhcpOutput)-1];
        $dnsAddresses = substr(explode(":", $lastLine)[1],1);
        $dnsAddresses = explode(" ", $dnsAddresses);
        runCommand(sudo()."chattr -i /etc/resolv.conf");
        for($i = 0; $i < count($dnsAddresses); $i++){

            $willBeWritten = "nameserver ".$dnsAddresses[$i];
            runCommand("echo ".$willBeWritten." >> /tmp/new_dns_addresses.txt");
        }
        runCommand(sudo()."cp /tmp/new_dns_addresses.txt /etc/resolv.conf");

        runCommand(sudo()."rm /etc/samba/smb.conf");
        runCommand(sudo()."systemctl stop samba4.service");
        return respond("Başarılı", 200);
    }

    function getTreeJSON(){

        $ldap= parent::getConnection(); //Returns a positive LDAP link identifier 
        $domainName= extensionDb('domainName');

        $filter = "objectClass=site";
        $result = ldap_search($ldap, "CN=Configuration,".$this->basedn, $filter,[
            "cn"
        ]);
        $samba_sites = ldap_get_entries($ldap,$result);

        $filter = "objectClass=server";
        $result = ldap_search($ldap, "CN=Configuration,".$this->basedn, $filter,[
            "serverReference",
            "cn"
        ]);

        $samba_servers = ldap_get_entries($ldap,$result);
        unset($samba_sites["count"]);
        unset($samba_servers["count"]);

        $arr = array();
        $arr["D: ".$domainName]=array();
        $arr["D: ".$domainName]['type']='Domain';
        foreach($samba_sites as $site){
            //print_r($site['cn'][0]."\n");
            $arr["D: ".$domainName]["S: ".$site['cn'][0]] = array();
            $arr["D: ".$domainName]["S: ".$site['cn'][0]]['type'] = 'Site';

            foreach($samba_servers as $server){
                if(str_contains($server['dn'], $site['cn'][0])){
                    if(isset($server['serverreference'][0])){
                        if(str_contains($server['serverreference'][0], "Domain Controllers")){
                            $arr["D: ".$domainName]["S: ".$site['cn'][0]]["DC: ".$server['cn'][0]] = array();
                            $arr["D: ".$domainName]["S: ".$site['cn'][0]]["DC: ".$server['cn'][0]]['type'] = 'DC';

                            //print_r("\t".$server['cn'][0]."\n");
                        }
                    }
                } 
            }
            
        }
        $json = json_encode($arr);
        return respond($json,200);
    }

    public function listDcs(){
        $ldap= parent::getConnection(); //Returns a positive LDAP link identifier 
        $filter = "(&(objectClass=server))";
        $result = ldap_search($ldap, "CN=Sites,CN=Configuration,".$this->basedn, $filter,[
            "name",
        ]);

        $dcs = ldap_get_entries($ldap,$result);
        $count = $dcs["count"];
        unset($dcs["count"]);

        $data = [];
        for($i=0 ; $i<$count ; $i++){
            $name = $dcs[$i]["dn"];
            $site = str_replace("CN=","",explode(",",$name)[2]);
            $data[] = [
                "dc" => str_replace("CN=","",explode(",",$name)[0]),
                "site" => $site,
                "name" => $name
            ];
        }

        return view('table', [
            "value" => $data,
            "onclick" => "showRepl",
            "title" => ["Etki Alanı Denetleyicisi","Site","*hidden*"],
            "display" => ["dc","site","name:name"],
        ]); 
    }

    public function listRepls(){
        $ldap= parent::getConnection(); //Returns a positive LDAP link identifier 
        $filter = "(&(fromServer=*))";
        $result = ldap_search($ldap, request("dn"), $filter,[
            "fromServer",
        ]);

        $dcs = ldap_get_entries($ldap,$result);
        unset($dcs["count"]);

        if(count($dcs) == 0){
            return respond(__("Bu DC'ye ait bağlantı bulunamadı !"),201);
        }
        
        foreach($dcs as $dc){
            $data[] = [
                "fromServer" => str_replace("CN=","",explode(",",$dc["fromserver"][0])[1]),
                "toServer" => str_replace("CN=","",explode(",",request("dn"))[0]),
            ];
        }

        return view('table', [
            "value" => $data,
            "title" => ["Nereden","Nereye"],
            "display" => ["fromServer","toServer"],
            "onclick" => "showReplModal",
            "menu" => [
                "Replike Et" => [
                    "target" => "showReplModal",  
                    "icon" => "fa-clone",             
                ],
            ]
        ]); 
    }

    function replicate(){
        $ldap= parent::getConnection();
        $choiceDict = array(
            "Root" => $this->basedn,
            "ForestDnsZones" => "DC=ForestDnsZones," . $this->basedn,
            "Configuration" => "CN=Configuration," . $this->basedn,
            "DomainDnsZones" => "DC=DomainDnsZones," . $this->basedn,
            "Schema" => "CN=Schema,CN=Configuration," . $this->basedn,
        );

        if(request("synctype") == true){
            $sync = "--full-sync";
        }
        else{
            $sync="";
        }

        $resp = Command::runSudo("samba-tool drs replicate @{:to} @{:from} @{:dn} @{:sync}",[
            "from" => request("fromServer"),
            "to" => request("toServer"),
            "dn" => $choiceDict[request("choice")],
            "sync" => $sync
        ]);

        if($resp == ""){
            return respond(__("Replikasyon yapılamadı !"),201);
        }
        else{
            return respond(__($resp),200);
        }
    }
}