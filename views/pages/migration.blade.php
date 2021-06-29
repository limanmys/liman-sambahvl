@component('modal-component',[
        "id" => "domainMigration",
        "title" => "Giriş",
        "footer" => [
            "text" => "Başlat",
            "class" => "btn-success",
            "onclick" => "startDomainMigration()"
        ]
    ])

    @include('inputs', [
        "inputs" => [
            "IP Adresi" => "ipAddr:text:Migrate edeceğiniz domainin kurulu olduğu sunucu IP adresini giriniz (192.168.1.10).",
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
        <a class="nav-link active" id="ldapLoginTab" href="#ldapLogin" data-toggle="tab">LDAP'a Bağlan</a>
      </li>

      <li class="nav-item">
        <a id="chooseSiteTab" class="nav-link" href="#chooseSite" data-toggle="tab" style="pointer-events: none;opacity: 0.4;">Site Seçimi</a>
      </li>

      <li class="nav-item">
        <a id="createSiteTab" class="nav-link" href="#createSite" data-toggle="tab" style="pointer-events: none;opacity: 0.4;">Site Oluştur</a>
      </li>
    </ul>

    <div class="tab-content">
    <div id="ldapLogin" class="tab-pane active">
      <form>
        <div class="form-group">
          <label for="migrateIpAdress">IP Adresi</label>
          <input class="form-control" id="migrateIpAdress" aria-describedby="migrateIpAdressHelp" placeholder="Ip adresi">
          <small id="migrateIpAdressHelp" class="form-text text-muted">Göç edeceğiniz sunucunun IP adresini giriniz (192.168.1.10).</small>
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
              Site seçiminizi aşağıdaki listeden yapabilirsiniz veya Site Oluştur tabından yeni bir site oluşturabilirsiniz.         
          </div>
      </div>
      <br />
      @include('inputs', [
          "inputs" => [
              "Site Listesi:select_site" => [
              ],
          ]
      ])
      <br />
      <br />
      <button class="btn btn-success" onclick="startSiteMigration()" style="float:right;">Başlat</button>

    </div>
    
    <div id="createSite" class="tab-pane bd-example">
      <div class="alert alert-primary d-flex align-items-center " role="alert">
        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
        <i class="fas fa-icon mr-2"></i>
        <div>
          Site oluşturmak için aşağıdaki butonu kullanabilirsiniz.
        </div>
      </div>
      <div class="form-group">
        <label for="newSiteName">Site adı</label>
        <input class="form-control" id="newSiteName" aria-describedby="newSiteNameHelp" placeholder="Yeni site adını giriniz.">
        <small id="newSiteNameHelp" class="form-text text-muted">Oluşturacağınız yeni site adını giriniz.</small>
      </div>
      <button class="btn btn-success" onclick="createNewSite()" style="float:right;">Oluştur</button>
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
            setTimeout(() => {
                observeMigration();
            }, 3000);
        }, function(response) {
          let error = JSON.parse(response);
           if(error["status"] == 202){
            $('#migrationLogs').append(error.message);
            refreshAfterLog();
           } else{
            $('#migrationLogs').append("\n\nKurulum sırasında hata oluştu.");
           }
        });
    }

    function showDomainMigration(){
        showSwal('Yükleniyor...','info',2000);
        $('#domainMigration').modal("show");
    }
    function startDomainMigration(){
        var form = new FormData();
        
        form.append("ip", $('#domainMigration').find('input[name=ipAddr]').val());
        form.append("username", $('#domainMigration').find('input[name=username]').val());
        form.append("password", $('#domainMigration').find('input[name=password]').val());

        request(API('migrate_domain'), form, function(response) {

          $('#migrationInfo').html("<b>Makine migrate ediliyor. Lütfen bekleyiniz.</b>");
          $('#domainMigration').modal("hide");
          showSwal('Migration işlemi başladı...', 'info', 3000);
          observeMigration();
            
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message,'error',5000);
        });
    }

    var ip,domainname,username,password;
    function ldapLogin(){
        var form = new FormData();
        
        ip = document.getElementById("migrateIpAdress").value;
        username = document.getElementById("migrateUsername").value;
        password = document.getElementById("migratePassword").value;
        form.append("ip",ip);
        form.append("username",username);
        form.append("password",password);
        
        request(API('ldap_login'), form, function(response) {
            message = JSON.parse(response)["message"];
            var sites = document.getElementById("[name=select_site]");
            if(message.length >= 1){
              listSitesAfterLogin();
              setActiveSiteTab();
            }else{
              console.log("No sites");
            }
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message,'error',5000);
        });
    }

    function listSitesAfterLogin(){
      $('[name=select_site]').find('option').remove();
      $.each(message, function(index, value){
        $('[name=select_site]').append($("<option>",{
          value: value,
          text: value
        }));
      }); 
    }

    function setActiveSiteTab(){
      document.getElementById("chooseSiteTab").style.pointerEvents = "auto";
      document.getElementById("chooseSiteTab").style.opacity = null;

      document.getElementById("createSiteTab").style.pointerEvents = "auto";
      document.getElementById("createSiteTab").style.opacity = null;

      showSwal('Bağlantı başarı ile kuruldu, lütfen site seçimi yapınız.','success',2000);
      $('.nav-tabs a[href="#chooseSite"]').tab('show');
    }

    function showSiteMigration(){
      $('#siteMigrate').modal("show");
    }
        
    function startSiteMigration(){

      var form = new FormData();
      let selectedSite = $('#siteMigrate').find('select[name=select_site]').val();
      form.append("site", site);
      form.append("ip",ip);
      form.append("username",username);
      form.append("domainname",domainname);
      form.append("password",password);
      request(API('migrate_site'), form, function(response) {
        
          $('#migrationInfo').html("<b>Makine migrate ediliyor. Lütfen bekleyiniz.</b>");
          showSwal('Migration işlemi başladı...', 'info', 3000);
          $('#siteMigrate').modal("hide");
          observeMigration();

      }, function(response){
          let error = JSON.parse(response);
          showSwal(error.message,'error',5000);
      });
      ip,domainname,username,password = null;

    }

    function createNewSite(){
        var form = new FormData();
        let newSiteName = $('#createSite').find('input[name=newSiteName]').val();
        form.append("newSiteName", newSiteName);
        request(API('create_site'), form, function(response) {
          showSwal('Site başarı ile oluşturuldu, site seçimi yapınız.','success',2000);
          $('.nav-tabs a[href="#chooseSite"]').tab('show');
          listSitesAfterLogin();
        }, function(error) {
            showSwal(error.message, 'error', 5000);
        });
    }

</script>