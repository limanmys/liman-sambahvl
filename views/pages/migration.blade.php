@component('modal-component',[
        "id" => "migrationModal",
        "title" => "Giriş",
        "footer" => [
            "text" => "OK",
            "class" => "btn-success",
            "onclick" => "hideMigrationModal()"
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
        "id" => "migrationModal2",
        "title" => "Giriş",
        "footer" => [
            "text" => "OK",
            "class" => "btn-success",
            "onclick" => "hideMigrationModal2()"
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

<br />
<div class="text-area" id="textarea"></div>
<br />
<button class="btn btn-success mb-2" id="btn3" onclick="showMigrationModal()" type="button">Migrate Et</button>
<button class="btn btn-success mb-2" id="btn4" onclick="showMigrationModal2()" type="button">Migrate Et - Site</button>
<pre id="migrationInfo">   </pre>

<script>

    function migration(){
        var form = new FormData();
        let x = document.getElementById("btn3");
        let y = document.getElementById("btn4");
        x.disabled = true;
        y.disabled = true;
        $('#textarea').html("Sunucu kontrol ediliyor lütfen bekleyiniz ... ");
        request(API('check_migrate'), form, function(response) {
            message = JSON.parse(response)["message"];
            if(message==false){
                x.disabled = true;
                y.disabled = true;
                $('#textarea').html("Bu sunucu bu işlemler için uygun değil.");
            }
            else{
                x.disabled = false;
                y.disabled = false;
                $('#textarea').html("Migration işlemi için aşağıdaki butonu kullanabilirsiniz.");
            }
        }, function(error) {
            showSwal(error.message, 'error', 5000);
        });
    }

    function showMigrationModal(){
        showSwal('Yükleniyor...','info',2000);
        $('#migrationModal').modal("show");
    }

    function hideMigrationModal(){

        var form = new FormData();
        $('#migrationModal').modal("hide");
        form.append("ip", $('#migrationModal').find('input[name=ipAddr]').val());
        form.append("username", $('#migrationModal').find('input[name=username]').val());
        form.append("password", $('#migrationModal').find('input[name=password]').val());
        showSwal('İşleminiz devam ediyor', 'info', 30000);
        
        request(API('migrate_domain'), form, function(response) {
            //message = JSON.parse(response)["message"];
            console.log(response);
            if(response == true){
                showSwal('Migration başarısız', 'error', 7000);
            }
            else if(response == false){
                migration();
                showSwal('Migration başarılı', 'success', 7000);
            }
            else if(response == ""){
                migration();
                showSwal('Migration başarılı', 'success', 7000);
            }
            else{
                showSwal('Migration başarısız...', 'error', 7000);
            }
        }, function(error) {
            showSwal(error.message, 'error', 5000);
        });
    }

    function showMigrationModal2(){
        showSwal('Yükleniyor...','info',2000);
        $('#migrationModal2').modal("show");
    }

    function hideMigrationModal2(){

        var form = new FormData();
        $('#migrationModal2').modal("hide");
        form.append("ip", $('#migrationModal2').find('input[name=ipAddr]').val());
        form.append("username", $('#migrationModal2').find('input[name=username]').val());
        form.append("password", $('#migrationModal2').find('input[name=password]').val());
        form.append("site", $('#migrationModal2').find('input[name=site]').val());
        showSwal('İşleminiz kontrol ediliyor...', 'info', 10000);

        request(API('migrate_site'), form, function(response) {
            showSwal('Migrate islemi basladi...', 'info', 3000);
            migrateLog("");
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
                showSwal("Migrate basari ile tamamlandi!",'success',2000);
                migration();
            }
        }, function(response){
          let error = JSON.parse(response);
          showSwal(error.message,'error',2000);
        });
    }

</script>