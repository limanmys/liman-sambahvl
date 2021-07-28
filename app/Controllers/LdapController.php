<?php
namespace App\Controllers;

class LdapController
{
    private $basedn = "";
    private $demote = 0;
    private $theIP = "";

	function connect(){
        $ip = ($this->demote == 0) ? $this->getIP() : $this->theIP;
        $domainname= strtolower(extensionDb('domainName'));
        $user = "administrator@".$domainname;
        $pass = extensionDb('domainPassword');
        $server = 'ldaps://'.$ip;
        $port="636";
        
        $str = explode(".",$domainname);
        $tmp = "";
        for($i=0 ; $i<count($str) ; $i++){
            if($str[$i] == end($str)){
                $tmp .= "DC=".$str[$i];
            }
            else{
                $tmp .= "DC=".$str[$i].",";
            }
        }
        $this->basedn = $tmp;
        $this->demote = 0;

        $ldap = ldap_connect($server);
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
        $bind=ldap_bind($ldap, $user, $pass);
        if (!$bind) {
            exit('Binding failed');
        }
        return $ldap;
    }

    function close($ldap){
        ldap_close($ldap);
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
        $ldap = $this->connect();

        $filter = "(&(objectClass=user)(objectCategory=person))";
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
        $this->close($ldap);

        return view('table', [
            "value" => $data,
            "title" => ["Kullanıcılar"],
            "display" => ["name"],
            "menu" => [

                "Sil" => [
                    "target" => "deleteUser",  
                    "icon" => "fa-trash-alt",             
                ],
                ]
    
        ]);

    }

    function deleteUser(){
        $user = request("name");
        $output=runCommand(sudo()."smbpasswd -x ". $user);
        return respond($output,200);
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
        $ldap = $this->connect();
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
            $data[] = [
                "name" => $nameItem
            ];
        }
        $this->close($ldap);
    
        return view('table', [
            "value" => $data,
            "title" => ["Gruplar"],
            "display" => ["name"],
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
    function listComputers(){
        $ldap = $this->connect();
    
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
        $this->close($ldap);
    
        return view('table', [
            "value" => $data,
            "title" => ["Bilgisayarlar"],
            "display" => ["name"]
        ]);
    
    }

    //Site
    function listSites(){

        $ldap = $this->connect();

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
        $this->close($ldap);

        return view('table', [
            "value" => $data,
            "title" => ["Sitelar"],
            "display" => ["name"],
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
                ],  
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

        $ldap = $this->connect();
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
        $this->close($ldap);

        return view('table', [
            "value" => $data,
            "title" => ["Sunucular"],
            "display" => ["name"],
        ]);
    }

    function addServerToSite(){

        $newSiteName = request("newSiteName");
        $ldap = $this->connect();
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
        $this->close($ldap);

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
        $ldap = $this->connect();

        $newRDN = substr($dnOfServer, 0, strpos($dnOfServer, ","));
        $newParent = "CN=Servers,CN=".$newSiteName.",CN=Sites,CN=Configuration,".$this->basedn;

        if(ldap_rename($ldap, $dnOfServer, $newRDN, $newParent, true)){
            $this->close($ldap);
            return respond("Success!",200);
        }
        else {
            $this->close($ldap);
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

        $this->close($ldap);
        
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
                ldap_close($ldapconn);

            } else {
                echo "LDAP bind anonymous failed...";
            }

        }
        return $domainName;
    }
    
    function listDemotable(){

        $hostNameOfThis = runCommand(sudo()."hostname");
        $ldap = $this->connect();
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

            $ldap = $this->connect();
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
            ldap_close($ldap);

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

        $ldap = $this->connect();
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
        ldap_close($ldap);

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
}
