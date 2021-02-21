<?php   
    include ("/liman/extensions/smbpydeb/views/writeFile.php");

    $resolv = $_POST["resolv"];
    if ($_POST["writeConfigFile"]){ 
        writeConfigFile($resolv);     
    }   
?>