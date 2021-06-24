<?php
namespace App\Controllers;

class LdapController
{
	function connect(){
        $domainname= "okkali.lab";
        $user = "administrator@".$domainname;
        $pass = "123123Aa";
        $server = 'ldaps://192.168.1.71';
        $port="636";
        $binddn = "DC=okkali,DC=lab";
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

    function listUsers(){
        $ldap = $this->connect();

        $filter = "objectClass=user";
        $result = ldap_search($ldap, "DC=okkali,DC=lab", $filter);
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
            "title" => ["Kullanıcı"],
            "display" => ["name"]
        ]);

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
        
        $result = ldap_search($ldap, "DC=okkali,DC=lab", $filter);
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
            "title" => ["Grup"],
            "display" => ["name"]
        ]);
    
    }
    function listComputers(){
        $ldap = $this->connect();
    
        $filter = "objectClass=computer";
        $result = ldap_search($ldap, "DC=okkali,DC=lab", $filter);
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
            "title" => ["Bilgisayar"],
            "display" => ["name"]
        ]);
    
    }

    //Site
    function listSites(){

        //$binddn = extensionDb('binddn');
        $binddn = "DC=okkali,DC=lab";

        $ldap = $this->connect();

        $filter = "objectClass=site";
        $result = ldap_search($ldap, "CN=Configuration,".$binddn, $filter);
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
            "title" => ["Sites"],
            "display" => ["name"],
            "menu" => [
                "Delete Site" => [
                    "target" => "deleteSite",
                    "icon" => "fas fa-trash-alt",
                ],  
                "Servers" => [
                    "target" => "showServersOfSite",
                    "icon" => "fas fa-server",
                ],  
                "Add Server" => [
                    "target" => "addServerToSite",
                    "icon" => "fas fa-plus",
                ],  
            ],
        ]);
    }

    function createSite(){

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
        //$binddn = extensionDb('binddn');
        $binddn = "DC=okkali,DC=lab";

        $ldap = $this->connect();
        $filter = "objectClass=server";

        $result = ldap_search($ldap, "CN=Configuration,".$binddn, $filter);
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
            "title" => ["Servers"],
            "display" => ["name"],
        ]);
    }

    function addServerToSite(){

        $newSiteName = request("newSiteName");
        //$binddn = extensionDb('binddn');
        $binddn = "DC=okkali,DC=lab";
        $ldap = $this->connect();
        $filter = "objectClass=server";
        $result = ldap_search($ldap, "CN=Configuration,".$binddn, $filter);
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
            "title" => ["Available Servers", "*hidden*", "*hidden*"],
            "display" => ["name", "dnOfServer:dnOfServer", "newSiteName:newSiteName"],
            "onclick" => "addThisServer"
        ]);
    }

    function addThisServer(){

        $dnOfServer = request("dnOfServer");
        $newSiteName = request("newSiteName");
        $ldap = $this->connect();
        //$binddn = extensionDb('binddn');
        $binddn = "DC=okkali,DC=lab";

        $newRDN = substr($dnOfServer, 0, strpos($dnOfServer, ","));
        $newParent = "CN=Servers,CN=".$newSiteName.",CN=Sites,CN=Configuration,".$binddn;

        if(ldap_rename($ldap, $dnOfServer, $newRDN, $newParent, true)){
            $this->close($ldap);
            return respond("Success!",200);
        }
        else {
            $this->close($ldap);
            return respond("Error!",201);
        }
    }
}
