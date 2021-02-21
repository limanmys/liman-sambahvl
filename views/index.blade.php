<ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
    <li class="nav-item">
        <a class="nav-link active"  onclick="tab1()" href="#tab1" data-toggle="tab">Kurulum</a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link "  onclick="tab2()" href="#tab2" data-toggle="tab">Samba Status</a>
    </li>

    <li class="nav-item">
        <a class="nav-link "  onclick="tab3()" href="#tab3" data-toggle="tab">NTP Status</a>
    </li>

    <li class="nav-item">
        <a class="nav-link "  onclick="tab4()" href="#tab4" data-toggle="tab">DNS Settings</a>
    </li>

</ul>


<div class="tab-content">
    <div id="tab1" class="tab-pane active">
        <button class="btn btn-primary mb-2" id="1" onclick="installSmbPackage()">SambaHVL Paketini Kur</button>
        <pre id="smbinstall">   </pre>
        <div id="smblast">  </div>

    </div>

    <div id="tab2" class="tab-pane">    </div>
    <div id="tab3" class="tab-pane">
    <pre id="ntplog">   </pre>
    </div>

    <div id="tab4" class="tab-pane">
        <form>
            <br>
            <label for="resolvlabel">Resolv.conf ip : </label>
            <input type="text" id="resolvip" name="resolv"><br><br>
            <label for="forwarderlabel">DNS Forwarder : </label>
            <input type="text" id="forwarderip"><br><br>

            <button class="btn btn-primary mb-2" onclick="writeConfigFile()" type="button">Submit</button>
        </form>
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

    function tab3(){
        var form = new FormData();
        request(API('ntpStatus'), form, function(response) {
            message = JSON.parse(response)["message"];
            if(message == true){
                isActiveButton = '<button type="button" class="btn btn-success" disabled>NTP Servisi Aktif !</button>' ;
                $('#tab3').html(isActiveButton);

                var d1 = document.getElementById('tab3');
                d1.insertAdjacentHTML('beforeend', '<pre id="ntplog">   </pre>');
                ntplog();
            } else{
                isActiveButton = '<button type="button" class="btn btn-danger" disabled>NTP Servisi Aktif Değil !</button>' ;
                $('#tab3').html(isActiveButton);

            }
        }, function(error) {
            $('#tab3').html("Hata oluştu");
        });
    }

    function ntplog(){
        var form = new FormData();
        request(API('ntplog'), form, function(response) {
            message = JSON.parse(response)["message"];
            $('#ntplog').html(message);
        }, function(error) {
            $('#ntplog').html("Hata oluştu");
        });
    }

    function tab4(){
        returnResolvIp();
        returnForwarderIp();
    }


    function returnResolvIp(){
        var form = new FormData();
        request(API('returnResolvIp'), form, function(response) {
            message = JSON.parse(response)["message"];
            document.getElementById("resolvip").value = message;
        }, function(error) {
            $('#tab4').html("Hata oluştu");
        });
    }

    function returnForwarderIp(){
        var form = new FormData();
        request(API('returnForwarderIp'), form, function(response) {
            message = JSON.parse(response)["message"];
            document.getElementById("forwarderip").value = message;
        }, function(error) {
            $('#tab4').html(error);
        });
    }

    function writeConfigFile(){
        var form = new FormData();
        var resolvinput = document.getElementById("resolvip").value;
        var forwarderinput = document.getElementById("forwarderip").value;
        form.append("resolvinput",resolvinput);
        form.append("forwarderinput",forwarderinput);

        request(API('writeConfigFile'), form, function(response) {
            message = JSON.parse(response)["message"];
            alert(message);
        }, function(error) {
            $('#tab4').html(error);
        });
        
    
    }   
     
</script>
