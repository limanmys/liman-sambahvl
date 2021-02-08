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
        $command = sudo() . "DEBIAN_FRONTEND=noninteractive apt install -y /tmp/smbpy.deb";
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
            $output = runScript("smbpy.py","",true);
            $respond_message = $res.$output;
            return respond($respond_message,200);
        }
    }

    
?>