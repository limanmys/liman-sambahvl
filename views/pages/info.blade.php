<div class="row">
  <div class="col-3">
    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
      <a class="nav-link active" onclick="getInfo()" id="v-pills-info-tab" data-toggle="pill" href="#v-pills-info" role="tab" aria-controls="v-pills-info" aria-selected="true">
      {{__('Detaylar')}}</a>
      <a class="nav-link" onclick="checkForUpdates()" id="v-pills-updates-tab" data-toggle="pill" href="#v-pills-updates" role="tab" aria-controls="v-pills-updates" aria-selected="false">
      {{__('Güncelleme')}}</a>
      <a class="nav-link" onclick="serviceStatus()" id="v-pills-servicestatus-tab" data-toggle="pill" href="#v-pills-servicestatus" role="tab" aria-controls="v-pills-servicestatus" aria-selected="false">
      {{__('Servis Durumu')}}</a>
      <a class="nav-link" onclick="verifyDomain()" id="v-pills-domainstatus-tab" data-toggle="pill" href="#v-pills-domainstatus" role="tab" aria-controls="v-pills-domainstatus" aria-selected="false">
      {{__('Etki Alanı')}}</a>
      <a class="nav-link" onclick="listPaths()" id="v-pills-paths-tab" data-toggle="pill" href="#v-pills-paths" role="tab" aria-controls="v-pills-paths" aria-selected="false">
      {{__('Pathler')}}</a>
      <a class="nav-link" onclick="listHave()" id="v-pills-have-tab" data-toggle="pill" href="#v-pills-have" role="tab" aria-controls="v-pills-have" aria-selected="false">
      {{__('Have')}}</a>
      <a class="nav-link" onclick="listBuildOptions()" id="v-pills-buildoptions-tab" data-toggle="pill" href="#v-pills-buildoptions" role="tab" aria-controls="v-pills-buildoptions" aria-selected="false">
      {{__('Build Seçenekleri')}}</a>
      <a class="nav-link" onclick="listWithOptions()" id="v-pills-withoptions-tab" data-toggle="pill" href="#v-pills-withoptions" role="tab" aria-controls="v-pills-withoptions" aria-selected="false">
      {{__('With Seçenekleri')}}</a>
      <a class="nav-link" onclick="listModules()" id="v-pills-modules-tab" data-toggle="pill" href="#v-pills-modules" role="tab" aria-controls="v-pills-modules" aria-selected="false">
      {{__('Moduller')}}</a>
      <a class="nav-link" onclick="getInstallLogs()" id="v-pills-logs-tab" data-toggle="pill" href="#v-pills-logs" role="tab" aria-controls="v-pills-logs" aria-selected="false">
      {{__('Loglar')}}</a>
      <a class="nav-link" onclick="showConfig()" id="v-pills-configuration-tab" data-toggle="pill" href="#v-pills-configuration" role="tab" aria-controls="v-pills-configuration" aria-selected="false">
      {{__('Konfigürasyon')}}</a>
      <a class="nav-link" id="v-pills-dnsupdate-tab" data-toggle="pill" href="#v-pills-dnsupdate" role="tab" aria-controls="v-pills-dnsupdate" aria-selected="false">
      {{__('DNS Güncelle')}}</a>

    </div>
  </div>
  <div class="col-9">
    <div class="tab-content" id="v-pills-tabContent">
      <div class="tab-pane fade show active" id="v-pills-info" role="tabpanel" aria-labelledby="v-pills-info-tab">
        <div class="row">
            <div class="col-sm-12" style="display: flex;
                align-items: center;
                justify-content: center;">
                @include("components.system-tree")
            </div>
            <div class="col-sm-5">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3 id="sambahvl_version"> </h3>
                        <p>SAMBAHVL Version</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-code-branch"></i>
                    </div>
                </div>

                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="version"> </h3>
                        <p>SAMBA Version</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-code-branch"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-7">
                <pre id="details"
                class="mx-2"
                        style="
                        border-radius: 5px;
                        background-color: black;
                        color: white;
                        font-size: medium; 
                        font-family: Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;
                        width: 100%;
                        word-wrap: break-word;
                        white-space: pre-wrap;"></pre>
            </div>
        </div>
        
      </div>

      <div class="tab-pane fade" id="v-pills-updates" role="tabpanel" aria-labelledby="v-pills-updates-tab">    
      </div>
      
      <div class="tab-pane fade" id="v-pills-servicestatus" role="tabpanel" aria-labelledby="v-pills-servicestatus-tab">
        <pre id="sambaLog"></pre>
      </div>

      <div class="tab-pane fade" id="v-pills-domainstatus" role="tabpanel" aria-labelledby="v-pills-domainstatus-tab">
      </div>

      <div class="tab-pane fade" id="v-pills-paths" role="tabpanel" aria-labelledby="v-pills-paths-tab">
        <div class="table-responsive" id="pathsTable"></div>
      </div>

      <div class="tab-pane fade" id="v-pills-have" role="tabpanel" aria-labelledby="v-pills-have-tab">
        <div class="table-responsive" id="haveTable"></div>
      </div>

      <div class="tab-pane fade" id="v-pills-buildoptions" role="tabpanel" aria-labelledby="v-pills-buildoptions-tab">
        <div class="table-responsive" id="buildOptionsTable"></div>
      </div>

      <div class="tab-pane fade" id="v-pills-buildoptions" role="tabpanel" aria-labelledby="v-pills-buildoptions-tab">
        <div class="table-responsive" id="buildOptionsTable"></div>
      </div>

      <div class="tab-pane fade" id="v-pills-withoptions" role="tabpanel" aria-labelledby="v-pills-withoptions-tab">
        <div class="table-responsive" id="withOptionsTable"></div>
      </div>

      <div class="tab-pane fade" id="v-pills-modules" role="tabpanel" aria-labelledby="v-pills-modules-tab">
        <div class="table-responsive" id="modulesTable"></div>
      </div>

      <div class="tab-pane fade" id="v-pills-logs" role="tabpanel" aria-labelledby="v-pills-logs-tab">
        <div class="row">
            <div class="col-sm-6">
                <div class="p-2 text-center ">
                    <h5>{{__('Kurulum')}}</h5>
                </div>
                    <pre id="install-logs" class="mx-2"
                        style="
                        border-radius: 5px;
                        background-color: black;
                        color: white;
                        font-size: medium; 
                        font-family: Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;
                        width: 100%;
                        overflow:auto;
                        height: 325px;"></pre>
            </div>
            <div class="col-sm-6">
                <div class="p-2 text-center ">
                    <h5>{{__('Etki Alanı')}}</h5>
                </div>
                    <pre id="other-logs" class="mx-2"
                        style="
                        border-radius: 5px;
                        background-color: black;
                        color: white;
                        font-size: medium; 
                        font-family: Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;
                        width: 100%;
                        overflow:auto;
                        height: 325px;"></pre>
            </div>
            </div>
      </div>

      <div class="tab-pane fade" id="v-pills-configuration" role="tabpanel" aria-labelledby="v-pills-configuration-tab">
        <div class="card-body">
                <h5 class="card-title">{{__("DNS Forward")}}</h5>
                <br><br>
                <input class="form-control" type="text" id="dnsForward">
                <br>
                <button onclick="changednsForward()" class="btn btn-primary">{{__("Güncelle")}}</button>
        </div>
        <pre id="conf"></pre>
    </div>

    <div class="tab-pane fade" id="v-pills-dnsupdate" role="tabpanel" aria-labelledby="v-pills-dnsupdate-tab">
        <div class="card-body">
                <div class="alert alert-primary d-flex align-items-center " role="alert" id="infoAlert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
                    <i class="fas fa-icon mr-2"></i>
                    <div>
                        {{__('Buton yardımıyla samba dns güncellemesi yapabilirsiniz.')}}
                    </div>
                </div>
                <br>
                <button onclick="DNSupdate()" class="btn btn-primary">{{__("Güncelle")}}</button>
        </div>   
        <div  style="height: 300px; width: 100%;">
             <pre id="dnsupdate-output" style="height: 100%; overflow-y: scroll;"></pre>
        </div> 
        
    </div>

    </div>
  </div>
</div>

<script>

    function getInfo(){
        var form = new FormData();

        request(API('get_samba_details'), form, function(response) {
            
            message = JSON.parse(response)["message"];
            if(message == ""){
                $('#details').html('{{__("Yüklü bir samba bulunamadı !")}}');
            }
            else{
                $('#details').html(message);

            }
            
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
        getVersion();
        getHvlVersion();
    }

    function getHvlVersion()
    {
        var form = new FormData();

        request(API('get_sambahvl_version'), form, function(response) {
            
            message = JSON.parse(response)["message"];
            if(message == ""){
                $('#sambahvl_version').html('{{__("Yüklü bir samba bulunamadı !")}}');
            }
            else{
                $('#sambahvl_version').html(message);

            }
            
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }
    
    function getVersion(){
        var form = new FormData();

        request(API('get_samba_version'), form, function(response) {
            
            message = JSON.parse(response)["message"];
            if(message == ""){
                $('#version').html('{{__("Yüklü bir samba bulunamadı !")}}');
            }
            else{
                $('#version').html(message);
            }
            
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
 
    }

    function serviceStatus(){
        var form = new FormData();
        request(API('return_samba_service_status'), form, function(response) {
            message = JSON.parse(response)["message"];
            if(message == true){
                isActive = '<div class="alert alert-success" role="alert">{{__("Samba Servisi Aktif")}}</div>' ;
                $('#v-pills-servicestatus').html(isActive);

                var d1 = document.getElementById('v-pills-servicestatus');
                d1.insertAdjacentHTML('beforeend', '<pre id="sambaLog">   </pre>');
                sambaLog();
            } else{
                isActive = '<div class="alert alert-danger" role="alert">{{__("Samba Servisi Aktif Değil !")}}</div>' ;
                $('#v-pills-servicestatus').html(isActive);

            }
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }

    function verifyDomain(){
        var form = new FormData();
        request(API('verify_domain'), form, function(response) {
            message = JSON.parse(response)["message"];
            if(message == true){
                isActive = '<div class="alert alert-success" role="alert">{{__("Etki Alanı Mevcut")}}</div>' ;
                $('#v-pills-domainstatus').html(isActive);
                returnDomainInformations();
            } else{
                isActive = '<div class="alert alert-danger" role="alert">{{__("Etki Alanı Mevcut Değil !")}}</div>' ;
                $('#v-pills-domainstatus').html(isActive);
            }
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }

    function returnDomainInformations(){
        var form = new FormData();
        request(API('return_domain_informations'), form, function(response) {
            message = JSON.parse(response)["message"];
            var d1 = document.getElementById('v-pills-domainstatus');
            d1.insertAdjacentHTML('beforeend', '<pre id="domainLog">   </pre>');
            $('#domainLog').html("\n" + message);
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }

    

    function sambaLog(){
        var form = new FormData();
        request(API('return_samba_service_log'), form, function(response) {
            message = JSON.parse(response)["message"];
            //console.log(message);
            $('#sambaLog').html(message);
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }

    function listPaths(){
        var form = new FormData();
        request(API('list_paths'), form, function(response) {
            $('#pathsTable').html(response).find('table').DataTable(dataTablePresets('normal'));

        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function listHave(){
        var form = new FormData();
        request(API('list_have'), form, function(response) {
            $('#haveTable').html(response).find('table').DataTable(dataTablePresets('normal'));
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function listBuildOptions(){
        var form = new FormData();
        request(API('list_build_options'), form, function(response) {
            $('#buildOptionsTable').html(response).find('table').DataTable(dataTablePresets('normal'));
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function listWithOptions(){
        var form = new FormData();
        request(API('list_with_options'), form, function(response) {
            $('#withOptionsTable').html(response).find('table').DataTable(dataTablePresets('normal'));
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function listModules(){
        var form = new FormData();
        request(API('list_modules'), form, function(response) {
            $('#modulesTable').html(response).find('table').DataTable(dataTablePresets('normal'));
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function getInstallLogs(){
        var form = new FormData();

        request(API('get_install_logs'), form, function(response) {
            
            message = JSON.parse(response)["message"];
            $('#install-logs').html(message);
            
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
        getOtherLogs();

    }

    function getOtherLogs(){
        var form = new FormData();

        request(API('get_other_logs'), form, function(response) {
            
            message = JSON.parse(response)["message"];
            $('#other-logs').html(message);
            
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function checkForUpdates(){
        var form = new FormData();

        request(API('check_for_updates'), form, function(response) {
            message = JSON.parse(response)["message"];
            console.log(message);
            if(message == "upgradable"){
                info = '<div class="alert alert-info" role="alert">{{__("Sambahvl güncel değil !")}}</div>' 
                +'<button class="btn btn-info mb-2" id="updateBtn" style="float:left;margin-left:10px;visibility:hidden;"></button>' 
                + '</br></br><pre id="updateLogs"></pre>';
                $('#v-pills-updates').html(info);

                let updateButton = document.getElementById("updateBtn");
                updateButton.onclick = function() {updateSambaPackage()};
                updateButton.innerText = '{{__("Sambahvl Paketini Güncelle")}}';
                updateButton.style.visibility = "visible";
            }
            else if(message == "not upgradable"){
                info = '<div class="alert alert-success" role="alert">{{__("Sambahvl güncel")}}</div>' ;
                $('#v-pills-updates').html(info);
            }
            else{
                info = '<div class="alert alert-danger" role="alert">{{__("Sambahvl yüklü değil !")}}</div>' ;
                $('#v-pills-updates').html(info); 
            }
            
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
        
    }

    function updateSambaPackage(){

        var form = new FormData();

        request(API('update_samba_package'), form, function(response) {
            let updateButton = document.getElementById("updateBtn");
            updateButton.disabled = true;
            observeUpdate();

        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);

        });
    }

    function observeUpdate(){
        var form = new FormData();

        request(API('observe_update'), form, function(response) {

            message = JSON.parse(response)["message"];
            $('#updateLogs').text(message);

            window.setInterval(function() {
            }, 1000);
            setTimeout(() => {
                observeUpdate();
            }, 3000);
            
            
        }, function(response) {
            let error = JSON.parse(response);
            $('#updateLogs').append(error.message);
        });
        

    }

    function showConfig(){  

        dnsForward();
        var form = new FormData();

        request(API('show_config'), form, function(response) {
            $('#conf').html(response);
            console.log(response);
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function dnsForward(){ 

        var form = new FormData();

        request(API('get_dnsForwarder'), form, function(response) {
            $('#dnsForward').val(response);
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }
    function changednsForward(){

        var data = new FormData();
        data.append("dnsForwardData", $("#dnsForward").val());

        request(API('change_DNSForwarder'), data, function(response) {
            showConfig();
            //$('#dnsForward').val(response);
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }

    function DNSupdate(){

        showSwal('{{__("Güncelleniyor...")}}','info');
        var form = new FormData();
        request(API('dnsupdate'), form, function(response) {   
            message = JSON.parse(response)["message"];
            $('#dnsupdate-output').html(message);
            Swal.close();
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

</script>
