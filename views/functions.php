
<?php 
    function index(){
        return view('index');
    }

    function verifyInstallation(){
        if(trim(runCommand('dpkg -s smbpy | grep "Status" | grep -w "install" 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
            return respond(true,200);
        }else{
            return respond(false,200);
        }
    }

    function verifyInstallationPhp(){
        if(trim(runCommand('dpkg -s smbpy | grep "Status" | grep -w "install" 1>/dev/null 2>/dev/null && echo "1" || echo "0"')) == "1"){
            return true;
        }else{
            return false;
        }

    }

    function putSmbPackage(){
        $dosya = '/tmp/smbpy.deb';

        if (file_exists($dosya)) {
            $res = "smbpy.deb zaten var !";
            return respond($res,200);
        } 

        else {
            putFile(getPath('public/smbpy.deb'), "/tmp/smbpy.deb"); 
            $res = "smbpy.deb başarı ile aktarıldı.";
            return respond($res,200);
        }
    }

    function installSmbPackage()
    {   
        putSmbPackage();
        $command = sudo() . "bash -c 'DEBIAN_FRONTEND=noninteractive apt install /tmp/smbpy.deb -qqy >/tmp/smbpyLog 2>&1 & disown'";
        ;
        runCommand($command);
        return respond("OK",200);
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

    function tab1(){
        # selam naber
    }

    function tab2(){
        $output = runCommand(sudo() . "systemctl is-active samba4.service");

        if (trim($output) == "active") {
            $status = '<button type="button" class="btn btn-success" disabled>Samba4 Servisi Aktif !</button>' ;
            
        } 
        else {
            $status = '<button type="button" class="btn btn-danger" disabled>Samba4 Servisi İnaktif !</button>' ;
            
        }

        return respond($status,200);
    }

    function ntpStatus(){
        $output = runCommand(sudo() . "systemctl is-active ntp.service");

        if (trim($output) == "active") {
            #$log = ntplog();
            #$status = '<button type="button" class="btn btn-success" disabled>NTP Servisi Aktif !</button> <br> '. $log;
            return respond(true,200);
        } 
        else {
            #$status = '<button type="button" class="btn btn-danger" disabled>NTP Servisi İnaktif !</button>' ;
            return respond(false,200);
        }

        #return respond($status,200);

    }

    function ntplog(){
        $command = "systemctl status ntp.service";

        $output = runCommand(sudo() . $command);

        return respond($output, 200);

    }

    function returnResolvIp(){
        $file = '/etc/resolv.conf';
        $searchfor = 'nameserver';
        header('Content-Type: text/plain');

        $contents = file_get_contents($file);
        $pattern = preg_quote($searchfor, '/');
        $pattern = "/^.*$pattern.*\$/m";

        if(preg_match_all($pattern, $contents, $matches)){
            $ip = $matches[0];
            $resolvip = str_replace("nameserver ", "", $ip);
            $resolvip = implode($resolvip);
        }
        else{
        echo "No matches found";
        }
        return respond($resolvip,200);
    }

    function returnForwarderIp(){
        $smbconf = parse_ini_file('/etc/samba/smb.conf',true);
        
        $forwarderip = ($smbconf['global']['dns forwarder']);

        return $forwarderip;
    }
?>
