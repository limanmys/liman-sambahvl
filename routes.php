<?php

return [
    "index" => "HomeController@index",

    "get_hostname" => "HostnameController@get",
    "set_hostname" => "HostnameController@set",
    "verify_installation" => "SambaController@verifyInstallation",
    "install_smb_package" => "SambaController@installSmbPackage",
    "delete_smb_package" => "SambaController@deleteSambaPackage",
    "observe_installation" => "SambaController@observeInstallation",
    "verify_domain" => "SambaController@verifyDomain",
    "create_samba_domain" => "SambaController@createSambaDomain",
    "return_domain_informations" => "SambaController@returnDomainInformations",
    "return_samba_service_status" => "SambaController@returnSambaServiceStatus",
    "return_samba_service_log" => "SambaController@sambaLog",

    "roles_table" => "SambaController@returnRolesTable",
    "take_the_role" => "SambaController@takeTheRole",
    "take_all_roles" => "SambaController@takeAllRoles",
    "seize_the_role" => "SambaController@seizeTheRole",

    "trusted_servers" => "SambaController@trustedServers",
    "destroy_trust_relation" => "SambaController@destroyTrustRelation",
    "create_trust_relation" => "SambaController@createTrustRelation",

    "replication_organized" => "SambaController@replicationOrganized",
    "create_bound" => "SambaController@createBound",
    "show_update_time" => "SambaController@showUpdateTime",

    "check_migrate" => "SambaController@checkMigrate",
    "migrate_domain" => "SambaController@migrateDomain",
    "migrate_site" => "SambaController@migrateSite",
    "migrate_log" => "SambaController@migrateLog",
    
    

];
