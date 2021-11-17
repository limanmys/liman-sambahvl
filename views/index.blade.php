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

    <li class="nav-item-installation" >
        <a class="nav-link active" onclick="tab1()" href="#tab1" data-toggle="tab">
        <i class="fas fa-download mr-2"></i>
        {{__('Kurulum')}}</a>
    </li>

    <li class="nav-item-operation" >
        <a class="nav-link" onclick="getInfo()" href="#info"  data-toggle="tab">
        <i class="fas fa-info mr-2"></i>
        {{__('Samba Bilgileri')}}</a>
    </li>

    <li class="nav-item-operation">
        <a class="nav-link" onclick="listUsers()" href="#users" data-toggle="tab">
        <i class="fas fa-user mr-2"></i>
        {{__('Kullanıcılar')}}</a>
    </li>

    <li class="nav-item-operation">
        <a class="nav-link" href="#groups" data-toggle="tab">
        <i class="fas fa-users mr-2"></i>
        {{__('Gruplar')}}</a>
    </li>

    <li class="nav-item-operation">
        <a class="nav-link" onclick="listComputers()" href="#computers"  data-toggle="tab">
        <i class="fas fa-desktop mr-2"></i>
        {{__('Bilgisayarlar')}}</a>
    </li>

    <li class="nav-item-operation">
        <a class="nav-link" href="#organizations"  data-toggle="tab">
        <i class="fas fa-sitemap mr-2"></i>
        {{__('Organizasyonlar')}}</a>
    </li>

    <li class="nav-item-operation">
        <a class="nav-link" onclick="printTable()" href="#fsmo" data-toggle="tab">
        <i class="fas fa-id-card mr-2"></i>
        {{__('FSMO Rol Yönetimi')}}</a>
    </li>

    <li class="nav-item-operation">
        <a class="nav-link" onclick="replicationInfo()" href="#replication" data-toggle="tab">
        <i class="fas fa-retweet mr-2"></i>
        {{__('Replikasyon Bilgisi')}}</a>
    </li>

    <li class="nav-item-operation">
        <a class="nav-link" onclick="listSites()" href="#sites"  data-toggle="tab">
        <i class="fas fa-network-wired mr-2"></i>
        {{__('Site Listesi')}}</a>
    </li>
    <!--
    <li class="nav-item-operation">
        <a class="nav-link " id = "demote_li" onclick="listDemotable()" href="#demote" data-toggle="tab">
        <i class="fas fa-unlink mr-2"></i>
        {{__('Etki Alanı Denetleyicisini Düşür')}}</a>
    </li>-->

    <li class="nav-item-operation">
        <a class="nav-link" onclick="showTables()" href="#repl"  data-toggle="tab">
        <i class="fas fa-atom mr-2"></i>
        {{__('Replike Et')}}</a>
    </li>

    <li class="nav-item-operation">
        <a class="nav-link" onclick="getClocks()" href="#clock"  data-toggle="tab">
        <i class="fas fa-clock mr-2"></i>
        {{__('Sistem Saati')}}</a>
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

    <div id="users" class="tab-pane">
        @include('pages.users')
    </div>

    <div id="groups" class="tab-pane">
        @include('pages.groups')
    </div>

    <div id="computers" class="tab-pane">
        @include('pages.computers')
    </div>

    <div id="organizations" class="tab-pane">
        @include('pages.organizations')
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

    <div id="repl" class="tab-pane">
        @include('pages.repl')
    </div>

    <!--
    <div id="demote" class="tab-pane">
        @include('pages.demote')
    </div>
    -->
    <div id="clock" class="tab-pane">
        @include('pages.clock')
    </div>
</div>

<script>


    $(".nav-item-installation").css("display", "none");
    $(".nav-item-operation").css("display", "none");

    checkSambahvl();

    function checkSambahvl(){
        var form = new FormData();
        request(API('check_sambahvl'), form, function(response) {
            msg = JSON.parse(response).message;
            if(msg){
                //if sambahvl exist check domain
                checkDomain();
            }
            else{
                //if sambahvl not exist display installation tab
                $(".nav-item-installation").css("display", "block"); 
            }
            
        }, function(error) {
            showSwal(error.message, 'error', 3000);
        });
    }

    function checkDomain(){
        var form = new FormData();
        request(API('check_domain'), form, function(response) {
            msg = JSON.parse(response).message;
            if(msg){
                // if domain exist show operation tabs and open info tab and call initial function
                $(".nav-item-operation").css("display", "block");
                $('.nav-tabs a[href="#info"]').tab('show');
                getInfo();
            }

        }, function(error) {
            showSwal(error.message, 'error', 3000);
        });
    }

    async function refreshAfterLog(){
      const { value: accept } = await Swal.fire({
        type: 'success',
        title: '{{__("Başarılı !")}}',
        text: '{{__("İşlem başarıyla gerçekleştirildi. Kayıtları Samba Bilgileri>Loglar sekmesinden görüntüleyebilirsiniz.")}}',
        confirmButtonText:
          '{{__("Devam et")}} <i class="fa fa-arrow-right"></i>',
      })

      if (accept) {
        window.location.reload();
      }
    }

    /*function demoted(){

        checkDomain();
        document.getElementById("tab1_li").style.display = "block";
        $('.nav-tabs a[href="#tab1"]').tab('show');
    }*/

    
</script>