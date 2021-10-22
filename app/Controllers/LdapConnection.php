<?php
namespace App\Controllers;

class LdapConnection
{
    private $connection;
    private $dn = "";
    private $domain = "";
    private $fqdn = "";
    private $ip = null;
    private $username = null;
    private $password = null;
    private $ssl = false;
    private $port = 389;

    public function __construct($ip, $username, $password, $ssl = false, $domain, $port)
    {

        $this->ip = $ip;
        $this->username = $username;
        $this->password = $password;
        $this->domain = $domain;
        $this->ssl = $ssl;
        $this->port = $port;
        if (substr($this->username, 0, 2) == "cn" || substr($this->username, 0, 2) == "CN") {
            $this->dn = $this->username;
        } else {
            $this->dn = $this->username . "@" . $this->getDomain();
        }

        $this->connection = $this->initWindows();
        
    }

    private function initWindows()
    {
        // Create Ldap Connection Object
        if ($this->ssl) {
            $ldap_connection = ldap_connect('ldaps://' . $this->ip, $this->port);
        } else {
            $ldap_connection = ldap_connect('ldap://' . $this->ip, $this->port);
        }

        // Set Protocol Version
        ldap_set_option($ldap_connection, 17, 3);

        ldap_set_option($ldap_connection, 24582, 0);

        ldap_set_option($ldap_connection, 8, 0);
        // Try to Bind Ldap
        try {
            $flag = ldap_bind($ldap_connection, $this->dn, $this->password);
        } catch (Exception $e) {
            die($e->getMessage());
        }

        if($flag == false){
            die($this->ip . " LDAP Baglantisi kurulamadi!");
        }

        // Return Object to use it later.
        return $ldap_connection;
    }

    public function getDomain()
    {
        $domain = $this->domain;
        $domain = str_replace("dc=", "", strtolower($domain));
        return str_replace(",", ".", $domain);
    }

    public function getDC()
    {
        return $this->domain;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function getFQDN()
    {
        return $this->fqdn;
    }
}