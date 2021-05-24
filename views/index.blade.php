<ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
    <li class="nav-item">
        <a class="nav-link active"  onclick="tab1()" href="#tab1" data-toggle="tab">Kurulum</a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link " onclick="tab5()" href="#tab5" data-toggle="tab">Etki Alanı Oluştur</a>
    </li>

    <li class="nav-item">
        <a class="nav-link "  onclick="tab2()" href="#tab2" data-toggle="tab">Samba Servis Durumu</a>
    </li>
</ul>


<div class="tab-content">
    <div id="tab1" class="tab-pane active">
        <p>SambaHVL paketini kurmak için lütfen aşağıdaki butonu kullanın.</p>
        <button class="btn btn-success mb-2" id="1" onclick="installSmbPackage()">SambaHVL Paketini Kur</button>
        <div id="smbInstallStatus">  </div>
        <pre id="smbinstall">   </pre>
        <div id="smblast">  </div>
    </div>

    <div id="tab2" class="tab-pane">   
        <pre id="sambaLog">   
        
        </pre>
    </div>

    <div id="tab5" class="tab-pane">  
        <p>Etki alanı kurmak için lütfen aşağıdaki butonu kullanın.</p>
        <button class="btn btn-success mb-2" id="createDomainButton" onclick="createDomain()" type="button">Etki Alanı Oluştur</button>
        <div id="domainStatus"></div> 
        <pre id="domainLogs" class="tab-pane">    
        </pre>
    </div>
</div>

<script>
   if(location.hash === ""){
        tab1();
    }

    // Install SambaHvl Package == Tab 1 == 

    function tab1(){
        var form = new FormData();
        request(API('verifyInstallation'), form, function(response) {
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
            $('#smblast').html(error);
           } else{
            showSwal(error, 'error', 3000);
           }

        });
    }

    // Create New Domain == Tab 2 ==

    function tab5(){
        var form = new FormData();
        request(API('verifyDomain'), form, function(response) {
            message = JSON.parse(response)["message"];
            let x = document.getElementById("createDomainButton");
            if(message == true){
                x.disabled = true;
                returnDomainInformations();
            } else{
                x.disabled = false;
            }
        }, function(error) {
            $('#tab1').html("Hata oluştu");
        });
    }

    
    function createDomain(){
        var form = new FormData();
        $('#domainStatus').html("<b>Etki alanı oluşturuluyor. Lütfen bekleyiniz.</b>");
        request(API('createSambaDomain'), form, function(response) {
            returnDomainInformations();
        }, function(error) {
            $('#smbinstall').html("Hata oluştu");
        });
    }
    

    function returnDomainInformations(){
        var form = new FormData();
        request(API('returnDomainInformations'), form, function(response) {
            message = JSON.parse(response)["message"];
            $('#domainStatus').html("<b>Etki alanı bilgileri :</b>");
            $('#domainLogs').html("\n" + message);
        }, function(error) {
            $('#tab2').html("Hata oluştu");
        });
    }

    // Control Samba4.service Status == Tab 3 ==

    function tab2(){
        var form = new FormData();
        request(API('tab2'), form, function(response) {
            message = JSON.parse(response)["message"];
            if(message == true){
                isActiveButton = '<button type="button" class="btn btn-success" disabled>Samba Servisi Aktif !</button>' ;
                $('#tab2').html(isActiveButton);

                var d1 = document.getElementById('tab2');
                d1.insertAdjacentHTML('beforeend', '<pre id="sambaLog">   </pre>');
                sambaLog();
            } else{
                isActiveButton = '<button type="button" class="btn btn-danger" disabled>Samba Servisi Aktif Değil !</button>' ;
                $('#tab2').html(isActiveButton);

            }
        }, function(error) {
            $('#tab3').html("Hata oluştu");
        });
    }

    function sambaLog(){
        var form = new FormData();
        request(API('sambaLog'), form, function(response) {
            message = JSON.parse(response)["message"];
            $('#sambaLog').html(message);
        }, function(error) {
            $('#sambaLog').html("Hata oluştu");
        });
    }
     
</script>