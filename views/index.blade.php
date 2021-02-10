<ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
    <li class="nav-item">
        <a class="nav-link active"  onclick="tab1()" href="#tab1" data-toggle="tab">Kurulum</a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link "  onclick="tab2()" href="#tab2" data-toggle="tab">Servis Durumu</a>
    </li>
</ul>

<div class="tab-content">
    <div id="tab1" class="tab-pane active">
        <button class="btn btn-primary mb-2" onclick="installSmbPackage()">SambaHVL Paketini Kur</button>
        <pre id="smbinstall">

        </pre>

        <pre id="smblast">

        </pre>

        
    </div>

    <div id="tab2" class="tab-pane">
    
    </div>
</div>

<script>

   if(location.hash === ""){
        tab1();
    }
    
    function installSmbPackage(){
        var form = new FormData();
        request(API('installSmbPackage'), form, function(response) {
            observe();
        }, function(error) {
            $('#smbinstall').html("Hata oluştu");
        });
    }
    
    function observe(){
        var form = new FormData();
        request(API('observeInstallation'), form, function(response) {
            let json = JSON.parse(response);
            setTimeout(() => {
                observe();
            }, 1000);
          $("#smbinstall").text(json["message"]);
        }, function(response) {
            let error = JSON.parse(response);
           if(error["status"] == 202){
            $('#smblast').html("Paket yüklendi");
           } else{
            $('#smblast').html("Hata oluştu");
           }
            
        });

    }

    function tab1(){
        var form = new FormData();
        request("{{API('tab1')}}", form, function(response) {
            message = JSON.parse(response)["message"];
            $('#tab1').html(message);
        }, function(error) {
            $('#tab1').html("Hata oluştu");
        });
    }

    function tab2(){
        var form = new FormData();
        request("{{API('tab2')}}", form, function(response) {
            message = JSON.parse(response)["message"];
            $('#tab2').html(message);
        }, function(error) {
            $('#tab2').html("Hata oluştu");
        });
    }
</script>

