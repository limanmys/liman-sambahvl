@component('modal-component',[
        "id" => "domainMigration",
        "title" => "Giriş",
        "footer" => [
            "text" => "OK",
            "class" => "btn-success",
            "onclick" => "hideDomainMigration()"
        ]
    ])
    @include('inputs', [
        "inputs" => [
            "IP Addresi" => "ipAddr:text",
            "Kullanıcı Adı" => "username:text",
            "Şifre" => "password:password"
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
            "IP Addresi" => "ipAddr:text",
            "Kullanıcı Adı" => "username:text",
            "Şifre" => "password:password",
            "İstenilen Site" => "site:site"
        ]
    ])
@endcomponent

<div class="text-area" id="checkInfo"></div>
<br />
<button class="btn btn-success mb-2" id="domain" onclick="showDomainMigration()" type="button">Migrate Et</button>
<button class="btn btn-success mb-2" id="site" onclick="showSiteMigration()" type="button">Migrate Et - Site</button>
<pre id="migrationInfo"></pre>

<script>

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

    function hideDomainMigration(){

        var form = new FormData();
        $('#domainMigration').modal("hide");
        form.append("ip", $('#domainMigration').find('input[name=ipAddr]').val());
        form.append("username", $('#domainMigration').find('input[name=username]').val());
        form.append("password", $('#domainMigration').find('input[name=password]').val());

        showSwal('İşleminiz kontrol ediliyor...', 'info');
        
        request(API('migrate_domain'), form, function(response) {

            showSwal('Migration işlemi başladı...', 'info', 3000);
            migrateLog();
            
        }, function(error) {
            showSwal(error.message, 'error', 5000);
        });
    }

    function showSiteMigration(){
        showSwal('Yükleniyor...','info',2000);
        $('#siteMigration').modal("show");
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
            migrateLog();

        }, function(response){
          let error = JSON.parse(response);
          showSwal(error.message,'error',2000);
        });
    }

    function migrateLog(){

        var form = new FormData();
        request(API('migrate_log'), form, function(response) {
            message = JSON.parse(response)["message"];
            if(message != "bitti"){
                console.log(message);
                $("#migrationInfo").text(message);
                setTimeout(() => {
                    migrateLog();
                }, 100);
            }
            else{
                showSwal("Migrate başarı ile tamamlandı !",'success',3000);
                checkMigrate();
            }
        }, function(response){
          let error = JSON.parse(response);
          showSwal(error.message,'error',2000);
        });
    }

</script>