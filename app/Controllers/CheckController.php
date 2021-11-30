<?php
namespace App\Controllers;

class CheckController
{
	function checkCertificateExists()
    {
        return respond(certificateExists(server()->ip_address, 636),200);
    }

    function checkCertificateValid()
    {
        return respond(isCertificateValid(server()->ip_address, 636),200);
    }

    function checkLdapConnection()
    {
        //return respond(true,200);
        if(ldapCheck(strtolower(extensionDb('domainName')), extensionDb('domainAdminUserName'), extensionDb('domainPassword'), server()->ip_address, 636))
            return respond(true,200);
        else
            return respond(false,200);

    }
}
