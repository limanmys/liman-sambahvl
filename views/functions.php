<?php 

    function index(){
        return view('index');
    }

    function verifyInstallation(){
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
        $command = sudo() . "DEBIAN_FRONTEND=noninteractive apt install -y /tmp/smbpy.deb > /tmp/smbpyLog -qqy >/tmp/smbpyLog 2>&1 & disown";
        runCommand($command);
    }

    function observeInstallation()
    {
        if(verifyInstallation() == true){
            $res = "smbHVL paketi zaten var !";
            return respond($res,200);
        }

        if(verifyInstallation() == false){
            installSmbPackage();
            $res = "smbHVL paketi başarı ile yüklendi.<br />";
            $log = runCommand(sudo() . "cat /tmp/smbpyLog)");
            
            return respond($log,200);
            #return respond($respond_message,200);
        }
    }

    function tab1(){
        # selam naber
    }

    function tab2(){
        $output = runCommand(sudo() . "systemctl is-active samba4.service");

        if (trim($output) == "active") {
            $status = '<td style="color: green;">Samba4 Servisi Aktif !</td>';
        } 
        else {
            $status = '<td style="color: red;">Samba4 Servisi İnaktif !</td>';
        }

        #$status = runCommand(sudo() . "systemctl status samba4.service");
        return respond($status,200);
    }

    function isactive($service) {
        
    }

    
?>