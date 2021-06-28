@component('modal-component',[
        "id" => "domainMigration",
        "title" => "Giriş",
        "footer" => [
            "text" => "Başlat",
            "class" => "btn-success",
            "onclick" => "hideDomainMigration()"
        ]
    ])

    @include('inputs', [
        "inputs" => [
            "IP Adresi" => "ipAddr:text:Migrate edeceğiniz domainin kurulu olduğu sunucu ip adresinı giriniz (192.168.1.10).",
            "Kullanıcı Adı" => "username:text:Migrate edilecek domain yetkili kullanıcısını giriniz (Administrator).",
            "Şifre" => "password:password:Migrate edilecek domain yetkili kullanıcısının parolasını giriniz."
        ]
    ])
@endcomponent

@component('modal-component',[
        "id" => "siteMigration",
        "title" => "Giriş",
        "footer" => [
            "text" => "OK",
            "class" => "btn-success",
            "onclick" => "hideSiteMigration()"
        ]
    ])
    @include('inputs', [
        "inputs" => [
            "IP Addresi" => "ipAddr:text:192.168.1.1",
            "Kullanıcı Adı" => "username:text:Administrator",
            "Şifre" => "password:password:Password",
            "Site:newSite" => [
                "forest" => "forest",
                "external" => "external"
            ],
        ]
    ])
    <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
        <li class="nav-item">
            <a class="nav-link active"  href="#deneme1" data-toggle="tab">
            <i class="fas fa-download mr-2"></i>
            Kurulum</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#deneme2"  data-toggle="tab">
            <i class="fas fa-info mr-2"></i>
            Samba Bilgileri</a>
        </li>
    </ul>
    <div class="tab-content">
        <div id="deneme1" class="tab-pane active">
            asdasdcajsdsa
        </div>
        <div id="deneme2" class="tab-pane">  
            sadsadsac
        </div>
    </div>

@endcomponent

<div class="p-3 text-center ">
    <h1 class="mb-3">Migration İşlemleri</h1>
</div>
<div class="alert alert-primary d-flex align-items-center " role="alert" id="infoAlert">
    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
    <i class="fas fa-icon mr-2"></i>
    <div>
    Migration işlemleri için aşağıdaki butonları kullanabilirsiniz.
    </div>
</div>
<div class="text-area" id="checkInfo"></div>
<br />
<button class="btn btn-success mb-2" id="domain" onclick="showDomainMigration()" type="button">Migrate Et</button>
<button class="btn btn-success mb-2" id="site" onclick="showSiteMigration()" type="button">Migrate Et - Site</button>
<pre id="migrationInfo"></pre>
<pre id="migrationLogs" style="overflow:auto;height:200px"> </pre>

<script>
    function observeMigration(){
        var form = new FormData();
        request(API('migrate_log'), form, function(response) {
            message = JSON.parse(response)["message"];
            $("#migrationLogs").text(message);
            window.setInterval(function() {
                var elem = document.getElementById('migrationLogs');
                elem.scrollTop = elem.scrollHeight;
            }, 1);
            if(message == "Kurulum başarıyla tamamlandı."){
                showSwal(message, 'success', 3000);
                testCreate()
            }
            else{
                setTimeout(() => {
                observeMigration();
            }, 3000);
            }
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }

    function hideDomainMigration(){
        var form = new FormData();
        $('#domainMigration').modal("hide");
        form.append("ip", $('#domainMigration').find('input[name=ipAddr]').val());
        form.append("username", $('#domainMigration').find('input[name=username]').val());
        form.append("password", $('#domainMigration').find('input[name=password]').val());

        $('#migrationInfo').html("<b>Makine migrate ediliyor. Lütfen bekleyiniz.</b>");
        
        request(API('migrate_domain'), form, function(response) {

            showSwal('Migration işlemi başladı...', 'info', 3000);
            observeMigration();
            
        }, function(error) {
            showSwal(error.message, 'error', 5000);
        });
    }

    function checkMigrate(){
        var form = new FormData();
        let domain_btn = document.getElementById("domain");
        let site_btn = document.getElementById("site");
        domain_btn.disabled = true;
        site_btn.disabled = true;

        $('#checkInfo').html("Sunucu kontrol ediliyor lütfen bekleyiniz ... ");

        request(API('check_migrate'), form, function(response) {
            message = JSON.parse(response)["message"];
            if(message==false){

                domain_btn.disabled = true;
                site_btn.disabled = true;
                $('#checkInfo').html("Bu sunucu bu işlemler için uygun değil.");
            }
            else{
                domain_btn.disabled = false;
                site_btn.disabled = false;
                $('#checkInfo').html("Migration için aşağıdaki butonları kullanabilirsiniz.");
            }
        }, function(error) {
            showSwal(error.message,'error',2000);
        });
    }

    function showDomainMigration(){
        showSwal('Yükleniyor...','info',2000);
        $('#domainMigration').modal("show");
    }
    
    

    function showSiteMigration(){
        showSwal('Yükleniyor...','info',2000);
        $('#siteMigration').modal("show");
        listSites2();
    }

    function hideSiteMigration(){
        var form = new FormData();
        $('#siteMigration').modal("hide");
        form.append("ip", $('#siteMigration').find('input[name=ipAddr]').val());
        form.append("username", $('#siteMigration').find('input[name=username]').val());
        form.append("password", $('#siteMigration').find('input[name=password]').val());
        form.append("site", $('#siteMigration').find('input[name=site]').val());

        showSwal('İşleminiz kontrol ediliyor...', 'info');

        request(API('migrate_site'), form, function(response) {

            showSwal('Migration işlemi başladı...', 'info', 3000);
            //migrateLog();

        }, function(response){
          let error = JSON.parse(response);
          showSwal(error.message,'error',2000);
        });
    }

    function listSites2(){
        var form = new FormData();
        request(API('list_sites2'), form, function(response) {
            message = JSON.parse(response)["message"];
            console.log(message);
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

</script>