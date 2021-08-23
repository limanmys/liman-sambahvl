<?php

return [
    "index" => "HomeController@index",
    "install_task" => "HostnameController@get",
    "get_hostname" => "HostnameController@get",
    "set_hostname" => "HostnameController@set",

    "runTask" => "TaskController@runTask",
    "checkTask" => "TaskController@checkTask",

    "verify_installation" => "SambaController@verifyInstallation",
    "check_installation" => "SambaController@checkInstallation",
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

    "migrate_domain" => "SambaController@migrateDomain",
    "migrate_site" => "SambaController@migrateSite",
    "migrate_log" => "SambaController@migrateLog",

    "list_users" => "LdapController@listUsers",
    "create_user" => "LdapController@createUser",
    "delete_user" => "LdapController@deleteUser",
    "delete_group" => "LdapController@deleteGroup",
    "list_groups" => "LdapController@listGroups",
    "create_group" => "LdapController@createGroup",
    "list_computers" => "LdapController@listComputers",
    "get_attributes" => "LdapController@getAttributes",
    "list_organizations" => "LdapController@listOrganizations",
    "create_computer" => "LdapController@createComputer",
    "delete_computer" => "LdapController@deleteComputer",
    "get_group_members" => "LdapController@getGroupMembers",
    
    "create_site" => "LdapController@createSite",
    "delete_site" => "LdapController@deleteSite",
    "list_sites" => "LdapController@listSites",
    "servers_of_site" => "LdapController@serversOfSite",
    "add_server_to_site" => "LdapController@addServerToSite",
    "add_this_server" => "LdapController@addThisServer",

    "check_samba_type" => "SambaController@checkSambaType",
    "list_paths" => "SambaController@listPaths",
    "list_have" => "SambaController@listHave",
    "list_build_options" => "SambaController@listBuildOptions",
    "list_with_options" => "SambaController@listWithOptions",
    "list_modules" => "SambaController@listModules",
    "get_samba_details" => "SambaController@getSambaDetails",
    "get_samba_version" => "SambaController@getSambaVersion",
    "get_sambahvl_version" => "SambaController@getSambaHvlVersion",

    "check_sambahvl" => "SambaController@checkSambahvl",
    "check_domain" => "SambaController@checkDomain",

    "get_system_clock" => "ClockController@getSystemClock",
    "get_hardware_clock" => "ClockController@getHardwareClock",

    "get_install_logs" => "SambaController@getInstallLogs",
    "get_other_logs" => "SambaController@getOtherLogs",

    "ldap_login" => "LdapController@ldapLogin",

    "demote_yourself" => "LdapController@demoteYourself",
    "only_configure_documents" => "LdapController@onlyConfigureDocuments",
    "list_demotable" => "LdapController@listDemotable",
    "demote_this_one" => "SambaController@demoteThisOne",
    "time_update" => "SambaController@timeUpdate",
    "test_new" => "SambaController@testNew",
    "return_the_ip" => "LdapController@returnTheIP",

    "check_for_updates" => "SambaController@checkForUpdates",
    "update_samba_package" => "SambaController@updateSambaPackage",
    "observe_update" => "SambaController@observeUpdate",

    "show_config" => "SambaController@showConfig",
    "get_dnsForwarder"=> "SambaController@getDNSForwarder",
    "change_DNSForwarder" => "SambaController@changeDNSForwarder",
    "dnsupdate" => "SambaController@DnsUpdate"
];
