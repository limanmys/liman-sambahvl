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
        "id" => "siteMigrate",
        "title" => "Migrate Site",
    ])

    <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
      <li class="nav-item">
        <a class="nav-link active" href="#ldapLogin" data-toggle="tab">Login Ldap</a>
      </li>

      <li class="nav-item">
        <a id="chooseSiteTab" class="nav-link" href="#chooseSite" data-toggle="tab" style="pointer-events: none;opacity: 0.4;">Choose Site</a>
      </li>
    </ul>

    <div class="tab-content">
    <div id="ldapLogin" class="tab-pane active">
      <form>
        <div class="form-group">
          <label for="migrateIpAdress">Ip Adresi</label>
          <input class="form-control" id="migrateIpAdress" aria-describedby="migrateIpAdressHelp" placeholder="Ip adresi">
          <small id="migrateIpAdressHelp" class="form-text text-muted">Göç edeceğiniz sunucunun IP adresini giriniz (192.168.1.10).</small>
        </div>
        <div class="form-group">
          <label for="migrateDomainName">Etki alanı adı</label>
          <input class="form-control" id="migrateDomainName" aria-describedby="migrateDomainNameHelp" placeholder="Etki alanı adı">
          <small id="migrateDomainNameHelp" class="form-text text-muted">Göç edeceğiniz etki alanının adını giriniz.</small>
        </div>
        <div class="form-group">
          <label for="migrateUsername">Kullanıcı adı</label>
          <input class="form-control" id="migrateUsername" aria-describedby="migrateUsernameHelp" placeholder="Kullanıcı adı">
          <small id="migrateUsernameHelp" class="form-text text-muted">Göç edeceğiniz sunucunun kullanıcı adını giriniz.</small>
        </div>
        <div class="form-group">
          <label for="migratePassword">Parola</label>
          <input type="password" class="form-control" id="migratePassword" placeholder="Parola">
          <small id="migrateIpAdressHelp" class="form-text text-muted">Göç edeceğiniz sunucunun kullanıcı parolasını giriniz.</small>
        </div>
      </form>
    <button class="btn btn-primary" onclick="ldapLogin()" style="float:right;">Bağlantıyı Kontrol Et <i class="fas fa-plug"></i></button>

    </div>

    <div id="chooseSite" class="tab-pane bd-example">
      <div class="alert alert-primary d-flex align-items-center " role="alert">
          <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
          <i class="fas fa-icon mr-2"></i>
          <div>
              Site seçiminizi aşağıdaki listeden yapabilirsiniz.
          </div>
      </div>
      <br />
      <form>
        <select class="form-select form-select-lg mb-3" id="select_site" aria-label=".form-select-lg example">
        </select>
      </form>
      <button class="btn btn-success" onclick="hideSiteMigration()" style="float:right;">Migrate</button>

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
<div id="migrationInfo"></div>
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
            }, 1000);
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
    var ip,domainname,username,password;
    function ldapLogin(){
        var form = new FormData();
        
        ip = document.getElementById("migrateIpAdress").value;
        domainname = document.getElementById("migrateDomainName").value;
        username = document.getElementById("migrateUsername").value;
        password = document.getElementById("migratePassword").value;
        form.append("ip",ip);
        form.append("domainname",domainname);
        form.append("username",username);
        form.append("password",password);
        
        request(API('ldap_login'), form, function(response) {
            message = JSON.parse(response)["message"];
            if(message.length >= 1){
              var sites = document.getElementById("select_site");
              for(var i = 0; i < message.length; i++){
                  var option = document.createElement("option");
                  option.text = message[i];
                  sites.add(option);
              }
              setActiveSiteTab();
            }else{
              console.log("No sites");
            }
        }, function(error) {
          error = JSON.parse(error);
            console.log(error.message);
        });
    }
    function setActiveSiteTab(){
      // burada
      document.getElementById("chooseSiteTab").style.pointerEvents = "auto";
      document.getElementById("chooseSiteTab").style.opacity = null;
      showSwal('Bağlantı başarı ile kuruldu, lütfen site seçimi yapınız.','success',2000);
    }
    function showSiteMigration(){
      $('#siteMigrate').modal("show");
    }
        

    function hideSiteMigration(){

      var form = new FormData();
      let selectedSite = document.getElementById("select_site").value;
      form.append("site", site);
      form.append("ip",ip);
      form.append("username",username);
      form.append("domainname",domainname);
      form.append("password",password);

      request(API('migrate_site'), form, function(response) {

          showSwal('Migration işlemi başladı...', 'info', 3000);
          $('#siteMigrate').modal("hide");
          migrateLog();

      }, function(response){
        let error = JSON.parse(response);
        showSwal(error.message,'error',2000);
      });
    }

</script>