<div class="row">
  <div class="col-3">
    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
      <a class="nav-link active" onclick="getInfo()" id="v-pills-info-tab" data-toggle="pill" href="#v-pills-info" role="tab" aria-controls="v-pills-info" aria-selected="true">Detaylar</a>
      <a class="nav-link" onclick="serviceStatus()" id="v-pills-servicestatus-tab" data-toggle="pill" href="#v-pills-servicestatus" role="tab" aria-controls="v-pills-servicestatus" aria-selected="false">Servis Durumu</a>
      <a class="nav-link" onclick="verifyDomain()" id="v-pills-domainstatus-tab" data-toggle="pill" href="#v-pills-domainstatus" role="tab" aria-controls="v-pills-domainstatus" aria-selected="false">Etki Alanı</a>
      <a class="nav-link" onclick="listPaths()" id="v-pills-paths-tab" data-toggle="pill" href="#v-pills-paths" role="tab" aria-controls="v-pills-paths" aria-selected="false">Paths</a>
      <a class="nav-link" onclick="listHave()" id="v-pills-have-tab" data-toggle="pill" href="#v-pills-have" role="tab" aria-controls="v-pills-have" aria-selected="false">Have</a>
      <a class="nav-link" onclick="listBuildOptions()" id="v-pills-buildoptions-tab" data-toggle="pill" href="#v-pills-buildoptions" role="tab" aria-controls="v-pills-buildoptions" aria-selected="false">Build Options</a>
      <a class="nav-link" onclick="listWithOptions()" id="v-pills-withoptions-tab" data-toggle="pill" href="#v-pills-withoptions" role="tab" aria-controls="v-pills-withoptions" aria-selected="false">With Options</a>
      <a class="nav-link" onclick="listModules()" id="v-pills-modules-tab" data-toggle="pill" href="#v-pills-modules" role="tab" aria-controls="v-pills-modules" aria-selected="false">Modules</a>
      <a class="nav-link" onclick="getInstallLogs()" id="v-pills-logs-tab" data-toggle="pill" href="#v-pills-logs" role="tab" aria-controls="v-pills-logs" aria-selected="false">Loglar</a>

    </div>
  </div>
  <div class="col-9">
    <div class="tab-content" id="v-pills-tabContent">
      <div class="tab-pane fade show active" id="v-pills-info" role="tabpanel" aria-labelledby="v-pills-info-tab">
        <div class="row">
        <div class="col-sm-6">
            <div class="card">
            <div class="card-body">
                <pre id="details"></pre>
            </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
            <div class="card-body">
                <pre id="version"></pre>
            </div>
            </div>
        </div>
        </div>

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
                <div class="card">
                <div class="p-2 text-center ">
                    <h5 class="mb-3">Kurulum</h5>
                </div>
                <div class="card-body">
                    <pre id="install-logs" style="height:325px;overflow:auto;"></pre>
                </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card">
                <div class="p-2 text-center ">
                    <h5 class="mb-3">Etki Alanı</h5>
                </div>
                <div class="card-body">
                    <pre id="other-logs" style="height:325px;overflow:auto;"></pre>
                </div>
                </div>
            </div>
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
            $('#details').html(message);
            
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
        getVersion();
        
    }
    
    function getVersion(){
        var form = new FormData();

        request(API('get_samba_version'), form, function(response) {
            
            message = JSON.parse(response)["message"];
            $('#version').html(message);
            
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
                isActive = '<div class="alert alert-success" role="alert">Samba Servisi Aktif</div>' ;
                $('#v-pills-servicestatus').html(isActive);

                var d1 = document.getElementById('v-pills-servicestatus');
                d1.insertAdjacentHTML('beforeend', '<pre id="sambaLog">   </pre>');
                sambaLog();
            } else{
                isActive = '<div class="alert alert-danger" role="alert">Samba Servisi Aktif Değil !</div>' ;
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
                isActive = '<div class="alert alert-success" role="alert">Etki Alanı Mevcut</div>' ;
                $('#v-pills-domainstatus').html(isActive);
                returnDomainInformations();
            } else{
                isActive = '<div class="alert alert-danger" role="alert">Etki Alanı Mevcut Değil !</div>' ;
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
            $('#pathsTable').html(response).find('table').DataTable({
            bFilter: true,
            "language" : {
                url : "/turkce.json"
            }
            });;

        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function listHave(){
        var form = new FormData();
        request(API('list_have'), form, function(response) {
            $('#haveTable').html(response).find('table').DataTable({
            bFilter: true,
            "language" : {
                url : "/turkce.json"
            }
            });;
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function listBuildOptions(){
        var form = new FormData();
        request(API('list_build_options'), form, function(response) {
            $('#buildOptionsTable').html(response).find('table').DataTable({
            bFilter: true,
            "language" : {
                url : "/turkce.json"
            }
            });;
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function listWithOptions(){
        var form = new FormData();
        request(API('list_with_options'), form, function(response) {
            $('#withOptionsTable').html(response).find('table').DataTable({
            bFilter: true,
            "language" : {
                url : "/turkce.json"
            }
            });;
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function listModules(){
        var form = new FormData();
        request(API('list_modules'), form, function(response) {
            $('#modulesTable').html(response).find('table').DataTable({
            bFilter: true,
            "language" : {
                url : "/turkce.json"
            }
            });;
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

</script>
