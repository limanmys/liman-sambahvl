<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
  </symbol>
  <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
  </symbol>
  <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
  </symbol>
</svg>

<ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
    <li class="nav-item">
        <a class="nav-link active" id="tab1_li" onclick="tab1()" href="#tab1" data-toggle="tab">
        <i class="fas fa-download mr-2"></i>
        Kurulum</a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link " onclick="tab2()" href="#tab2" data-toggle="tab">
        <i class="fas fa-rss-square mr-2"></i>
        Etki Alanı Oluştur</a>
    </li>

    <li class="nav-item">
        <a class="nav-link" onclick="getInfo()" href="#info"  data-toggle="tab">
        <i class="fas fa-info mr-2"></i>
        Samba Bilgileri</a>
    </li>

    <li class="nav-item">
        <a class="nav-link " onclick="checkMigrate()" href="#migration"  data-toggle="tab">
        <i class="fas fa-bezier-curve mr-2"></i>
        Migration</a>
    </li>

    <li class="nav-item">
        <a class="nav-link "  onclick="listUsers()" href="#users" data-toggle="tab">
        <i class="fas fa-user mr-2"></i>
        Kullanıcılar</a>
    </li>

    <li class="nav-item">
        <a class="nav-link " href="#groups" data-toggle="tab">
        <i class="fas fa-users mr-2"></i>
        Gruplar</a>
    </li>

    <li class="nav-item">
        <a class="nav-link "onclick="listComputers()" href="#computers"  data-toggle="tab">
        <i class="fas fa-desktop mr-2"></i>
        Bilgisayarlar</a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link "  id="fsmo_li" onclick="printTable()" href="#fsmo" data-toggle="tab">
        <i class="fas fa-id-card mr-2"></i>
        FSMO Rol Yönetimi</a>
    </li>

    <li class="nav-item">
        <a class="nav-link " id="replication_li" onclick="replicationInfo()" href="#replication" data-toggle="tab">
        <i class="fas fa-retweet mr-2"></i>
        Replikasyon Bilgisi</a>
    </li>

    <li class="nav-item">
        <a class="nav-link" id="sites_li" onclick="listSites()" href="#sites"  data-toggle="tab">
        <i class="fas fa-network-wired mr-2"></i>
        Site Listesi</a>
    </li>

    

</ul>

<div class="tab-content">
    <div id="tab1" class="tab-pane active">
        @include('pages.install')
    </div>

    <div id="tab2" class="tab-pane">  
        @include('pages.domain')
    </div>

    <div id="info" class="tab-pane">
        @include('pages.info')
    </div>

    <div id="migration" class="tab-pane">
        @include('pages.migration')
    </div>

    <div id="users" class="tab-pane">
        @include('pages.users')
    </div>

    <div id="groups" class="tab-pane">
        @include('pages.groups')
    </div>

    <div id="computers" class="tab-pane">
        @include('pages.computers')
    </div>

    <div id="fsmo" class="tab-pane">
        @include('pages.fsmo')
    </div>

    <div id="replication" class="tab-pane">
        @include('pages.replication')
    </div>

    <div id="sites" class="tab-pane">
        @include('pages.sites')
    </div>

    

</div>

<script>

    if(location.hash === ""){
        tab1();
    }
    check();

    function check(){
        checkSambahvl();
        
    }
    function checkSambahvl(){
        var form = new FormData();
        request(API('check_sambahvl'), form, function(response) {
            message = JSON.parse(response)["message"];
            console.log(message);
            if(!message){
                let e1 = document.getElementById("fsmo_li");
                e1.style.display = "none";
                let e2 = document.getElementById("replication_li");
                e2.style.display = "none";
                let e3 = document.getElementById("sites_li");
                e3.style.display = "none";

            }
            else{
                checkDomain();
            }
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
        
    }
    function checkDomain(){
        var form = new FormData();
        request(API('check_domain'), form, function(response) {
            message = JSON.parse(response)["message"];
            console.log(message);
            if(!message){
                let e1 = document.getElementById("fsmo_li");
                e1.style.display = "none";
                let e2 = document.getElementById("replication_li");
                e2.style.display = "none";
                let e3 = document.getElementById("sites_li");                
                e3.style.display = "none";

            }
            else{
                let e1 = document.getElementById("tab1_li");
                e1.style.display = "none";
            }
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
        
    }

    
</script>