<div id="errorDiv" style="visibility:none;"></div>
<div class="alert alert-primary d-flex align-items-center" role="alert">
  <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:" style="margin-right:1%;"><use xlink:href="#info-fill"/></svg>
  <div>
  <p>SambaHVL paketini kurmak için lütfen aşağıdaki butonu kullanın.</p>
  </div>
</div>

<button class="btn btn-success mb-2" id="install" onclick="installSmbPackage()" style="float:left;">SambaHVL Paketini Kur</button>
<button class="btn btn-danger mb-2" id="delete" style="float:left;margin-left:10px;visibility:hidden;"></button>
<div id="smbInstallStatus"> </div>
<pre id="smbinstall" style="margin-top:5%;"></pre>
<div id="smblast">  </div>

<script>
// Install SambaHvl Package == Tab 1 == 

    function tab1(){
        var form = new FormData();
        request(API('verify_installation'), form, function(response) {
            $('#smblast').html("");
            message = JSON.parse(response)["message"];
            let x = document.getElementById("install");
            if(message == true){
                x.disabled = true;
                $('#smbinstall').html("\nPaket zaten yüklü !");
            } else{
                x.disabled = false;
            }
        }, function(error) {
            let x = document.getElementById("install");
            x.disabled = true;

            $('#errorDiv').html(
                '<div class="alert alert-danger d-flex align-items-center"  role="alert">' +
                    '<svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill" /></svg>' +
                    '<div>'+
                        'Hata : Sunucuda kurulu Samba Paketi tespit edildi !'+
                    '</div>'+
                '</div>');
            
                let deleteButton = document.getElementById("delete");
                deleteButton.onclick = function() {deleteSambaPackage()};
                deleteButton.innerText = "Samba Paketini Kaldır";
                deleteButton.style.visibility = "visible";
        });
    }

    function deleteSambaPackage(){
        var form = new FormData();
        showSwal("Samba paketi kaldırılıyor.", 'info', 3000);

        request(API('delete_smb_package'), form, function(response) {
            showSwal("Paket başarıyla kaldırıldı !", 'success', 3000);
            window.location.reload();
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }

    function installSmbPackage(){
        var form = new FormData();
        $('#smbInstallStatus').html("<b>SambaHvl kuruluyor. Lütfen kayıtları takip ediniz.</b>");
        request(API('install_smb_package'), form, function(response) {
            observe();
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }
    
    function observe(){
        var form = new FormData();
        request(API('observe_installation'), form, function(response) {
            let json = JSON.parse(response);
            setTimeout(() => {
                observe();
            }, 1000);
          $("#smbinstall").text(json["message"]);
        }, function(response) {
            let error = JSON.parse(response);
            if(error["status"] == 202){
            $('#smblast').html(error);
           } else{
            showSwal(error, 'error', 3000);
           }

        });
    }
</script>
