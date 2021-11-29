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
        return respond(ldapCheck(strtolower(extensionDb('domainName')), "administrator", extensionDb('domainPassword'), server()->ip_address, 636),200);
    }
}
