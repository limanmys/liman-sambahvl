<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
  </symbol>
</svg>


<div id="errorDiv" style="visibility:none;">
    
</div>

<p>SambaHVL paketini kurmak için lütfen aşağıdaki butonu kullanın.</p>
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
