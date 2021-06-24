<div class="row">
  <div class="col-3">
    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
      <a class="nav-link active" onclick="info()" id="v-pills-info-tab" data-toggle="pill" href="#v-pills-info" role="tab" aria-controls="v-pills-info" aria-selected="true">Info</a>
      <a class="nav-link" onclick="serviceStatus()" id="v-pills-servicestatus-tab" data-toggle="pill" href="#v-pills-servicestatus" role="tab" aria-controls="v-pills-servicestatus" aria-selected="false">Servis Durumu</a>
      <a class="nav-link" onclick="listPaths()" id="v-pills-paths-tab" data-toggle="pill" href="#v-pills-paths" role="tab" aria-controls="v-pills-paths" aria-selected="false">Paths</a>
      <a class="nav-link" onclick="listHave()" id="v-pills-have-tab" data-toggle="pill" href="#v-pills-have" role="tab" aria-controls="v-pills-have" aria-selected="false">Have</a>
      <a class="nav-link" onclick="listBuildOptions()" id="v-pills-buildoptions-tab" data-toggle="pill" href="#v-pills-buildoptions" role="tab" aria-controls="v-pills-buildoptions" aria-selected="false">Build Options</a>
      <a class="nav-link" onclick="listWithOptions()" id="v-pills-withoptions-tab" data-toggle="pill" href="#v-pills-withoptions" role="tab" aria-controls="v-pills-withoptions" aria-selected="false">With Options</a>
      <a class="nav-link" onclick="listModules()" id="v-pills-modules-tab" data-toggle="pill" href="#v-pills-modules" role="tab" aria-controls="v-pills-modules" aria-selected="false">Modules</a>

    </div>
  </div>
  <div class="col-9">
    <div class="tab-content" id="v-pills-tabContent">
      <div class="tab-pane fade show active" id="v-pills-info" role="tabpanel" aria-labelledby="v-pills-info-tab">
        <!-- This text will not be displayed on the Web page 
        <h5 class="card-title">Detaylar</h5>
        <pre id="type"></pre>
        <pre id="details"></pre>

        <h5 class="card-title">Samba Build Version</h5>
        -->  
        <div class="row">
        <div class="col-sm-6">
            <div class="card">
            <div class="card-body">
                <h5 class="card-title">Detaylar</h5>
                <pre id="details"></pre>
            </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
            <div class="card-body">
                <h5 class="card-title">Build Versiyonu</h5>
                <pre id="version"></pre>
            </div>
            </div>
        </div>
        </div>

      </div>

      <div class="tab-pane fade" id="v-pills-servicestatus" role="tabpanel" aria-labelledby="v-pills-servicestatus-tab">
        <pre id="sambaLog">   </pre>
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

    </div>
  </div>
</div>

<script>

    function info(){
        showSwal('Yükleniyor...','info',2000);
        var form = new FormData();

        request(API('samba_details'), form, function(response) {
            
            message = JSON.parse(response)["message"];
            $('#details').html(message);
            
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });

        
    }
    function info(){
        showSwal('Yükleniyor...','info',2000);
        var form = new FormData();

        request(API('get_samba_version'), form, function(response) {
            
            message = JSON.parse(response)["message"];
            $('#details').html(message);
            
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
                isActiveButton = '<div class="alert alert-success" role="alert">Samba Servisi Aktif</div>' ;
                $('#v-pills-servicestatus').html(isActiveButton);

                var d1 = document.getElementById('v-pills-servicestatus');
                d1.insertAdjacentHTML('beforeend', '<pre id="sambaLog">   </pre>');
                sambaLog();
            } else{
                isActiveButton = '<div class="alert alert-danger" role="alert">Samba Servisi Aktif Değil !</div>' ;
                $('#v-pills-servicestatus').html(isActiveButton);

            }
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }

    function sambaLog(){
        var form = new FormData();
        request(API('return_samba_service_log'), form, function(response) {
            message = JSON.parse(response)["message"];
            console.log(message);
            $('#sambaLog').html(message);
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }

    function listPaths(){
        showSwal('Yükleniyor...','info',2000);
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
        showSwal('Yükleniyor...','info',2000);
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
        showSwal('Yükleniyor...','info',2000);
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
        showSwal('Yükleniyor...','info',2000);
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
        showSwal('Yükleniyor...','info',2000);
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
</script>
