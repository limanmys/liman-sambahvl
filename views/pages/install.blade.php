<p>SambaHVL paketini kurmak için lütfen aşağıdaki butonu kullanın.</p>
<button class="btn btn-success mb-2" id="1" onclick="installSmbPackage()">SambaHVL Paketini Kur</button>
<div id="smbInstallStatus">  </div>
<pre id="smbinstall">   </pre>
<div id="smblast">  </div>

<script>
// Install SambaHvl Package == Tab 1 == 

    function tab1(){
        var form = new FormData();
        request(API('verify_installation'), form, function(response) {
            $('#smblast').html("");
            message = JSON.parse(response)["message"];
            let x = document.getElementById("1");
            if(message == true){
                x.disabled = true;
                $('#smbinstall').html("\nPaket zaten yüklü !");
            } else{
                x.disabled = false;
            }
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
