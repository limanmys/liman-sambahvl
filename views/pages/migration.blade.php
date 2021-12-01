<div class="p-3 text-center ">
    <h2 class="mb-3"
        style="
        text-transform: uppercase;
        font-weight: 700;"
    >{{__('Migration İşlemi')}}</h2>
    <p>
    {{__('Migration için aşağıdaki butonu kullanabilirsiniz.')}}
    </p>
</div>
<div class="text-area" id="checkInfo"></div>
<div class="mycontainer" style="width: 100%; display: flex; align-items:center; justify-content: center;">
  <button class="btn btn-primary mb-2" id="site" onclick="showSiteMigration()" type="button" style="padding: .5rem 2rem;
      font-size: 1.25rem;
      line-height: 1.5;
      border-radius: .3rem;"><i class="fas fa-people-carry mr-2"></i> {{__('Migrate Et')}}</button>
</div>
<div id="migrationInfo"></div>

<script>


    function observeMigration(){
        var form = new FormData();
        $("#sambaHvlLogs").css("display", "inline-block");
        request(API('migrate_log'), form, function(response) {
            message = JSON.parse(response)["message"];
            $("#sambaHvlLogs").text(message);
            window.setInterval(function() {
                var elem = document.getElementById('sambaHvlLogs');
                elem.scrollTop = elem.scrollHeight;
            }, 1000);
            setTimeout(() => {
                observeMigration();
            }, 3000);
        }, function(response) {
          let error = JSON.parse(response);
           if(error["status"] == 202){
            $('#sambaHvlLogs').append(error.message);
            refreshAfterLog();
           } else{
            $('#sambaHvlLogs').append("\n\nKurulum sırasında hata oluştu.");
           }
           let x = document.getElementById("createDomainButton");
            let y = document.getElementById("site");
            x.disabled = false;
            y.disabled = false;
        });
    }

    var ip,username,password;
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

      showSwal('{{__("Bağlantı başarı ile kuruldu, lütfen site seçimi yapınız.")}}','success',2000);
      $('.nav-tabs a[href="#chooseSite"]').tab('show');
    }

    function showSiteMigration(){
      $('#siteMigrate').modal("show");
    }
        
    function startSiteMigration(){
  
      let x = document.getElementById("createDomainButton");
      let y = document.getElementById("site");
      x.disabled = true;
      y.disabled = true;
      var form = new FormData();
      let selectedSite = $('#siteMigrate').find('select[name=select_site]').val();
      form.append("site", selectedSite);
      form.append("ip",ip);
      form.append("username",username);
      form.append("password",password);
      showSwal("Yükleniyor...",'info',3000);
      $('#siteMigrate').modal("hide");
      
      request(API('migrate_site'), form, function(response) {
        
          $('#migrationInfo').html("<b> {{__('Makine migrate ediliyor. Lütfen bekleyiniz.')}}</b>");
          showSwal('{{__("Migration işlemi başladı...")}}', 'info', 3000);
          observeMigration();

      }, function(response){
          let error = JSON.parse(response);
          showSwal(error.message,'error',5000);
          x.disabled = false;
          y.disabled = false;
      });
      ip,username,password = null;

    }

    
</script>