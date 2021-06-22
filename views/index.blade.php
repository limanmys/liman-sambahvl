@component('modal-component',[
        "id" => "createTrustRelationModal",
        "title" => "Create Trust Relation",
        "footer" => [
            "text" => "Create",
            "class" => "btn-success",
            "onclick" => "createTrustRelation()"
        ]
    ])
    @include('inputs', [
        "inputs" => [
            "Domain Name" => "newDomainName:text:deneme.lab",
            "IP Address" => "newIpAddr:text",
            "Type:newType" => [
                "forest" => "forest",
                "external" => "external"
            ],
            "Direction:newDirection" => [
                "incoming" => "incoming",
                "outgoing" => "outgoing",
                "both" => "both"
            ],
            "Create Location:newCreateLocation" => [
                "local" => "local",
                "both" => "both"
            ],
            "Username" => "newUsername:text",
            "Password" => "password:password"
        ]
    ])
@endcomponent

@component('modal-component',[
        "id" => "trustedServerDetailsModal",
        "title" => "Details",
        "footer" => [
            "text" => "Close",
            "class" => "btn-success",
            "onclick" => "closeTrustedServerDetailsModal()"
        ]
    ])
@endcomponent

@component('modal-component',[
        "id" => "deleteTrustedServerModal",
        "title" => "Warning",
        "footer" => [
            "text" => "Cancel",
            "class" => "btn-success",
            "onclick" => "closeTrustedServerDetailsModal()"
        ],
        "footer" => [
            "text" => "Delete",
            "class" => "btn-danger",
            "onclick" => "destroyTrustRelation()"
        ]
    ])
        @include('inputs', [
        "inputs" => [
            "Password" => "password:password"
        ]
    ])
@endcomponent





<ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
    <li class="nav-item">
        <a class="nav-link active"  onclick="tab1()" href="#tab1" data-toggle="tab">Kurulum</a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link " onclick="tab2()" href="#tab2" data-toggle="tab">Etki Alanı Oluştur</a>
    </li>

    <li class="nav-item">
        <a class="nav-link "  onclick="tab3()" href="#tab3" data-toggle="tab">Samba Servis Durumu</a>
    </li>

    <li class="nav-item">
        <a class="nav-link "  onclick="printTable()" href="#fsmo" data-toggle="tab">FSMO Rol Yönetimi</a>
    </li>

    <li class="nav-item">
        <a class="nav-link "  onclick="trustedServers()" href="#trustRelation" data-toggle="tab">Trusted Servers</a>
    </li>

    <li class="nav-item">
        <a class="nav-link "  onclick="replicationInfo()" href="#replication" data-toggle="tab">Replication Info</a>
    </li>
    <li class="nav-item">
        <a class="nav-link " onclick="migration()" href="#migration"  data-toggle="tab">Migration İşlemi</a>
    </li>
</ul>


<div class="tab-content">
    <div id="tab1" class="tab-pane active">
        @include('pages.installation')
    </div>

    <div id="tab2" class="tab-pane">  
        @include('pages.createdomain')
    </div>

    <div id="tab3" class="tab-pane">   
        <pre id="sambaLog">   
        
        </pre>
    </div>

    <div id="fsmo" class="tab-pane">
        @include('pages.fsmo')
    </div>

    <div id="trustRelation" class="tab-pane">
        <button class="btn btn-success mb-2" id="createButton" onclick="showCreateTrustRelationModal()" type="button">Create</button>    
        <div id="trustedServers">
        </div>
    </div>
    
    <div id="replication" class="tab-pane">
        <div id="replicationPrintArea"></div> 
        <div class="table-responsive replicationTable" id="replicationTable"></div> 
    </div>

    <div id="migration" class="tab-pane">
        @include('pages.migration')
    </div>

</div>

<script>
    var domainName = "";
    var passwd = "";

    if(location.hash === ""){
        tab1();
    }

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

    // Create New Domain == Tab 2 ==

    function tab2(){
        var form = new FormData();
        request(API('verify_domain'), form, function(response) {
            message = JSON.parse(response)["message"];
            let x = document.getElementById("createDomainButton");
            if(message == true){
                x.disabled = true;
                returnDomainInformations();
            } else{
                x.disabled = false;
            }
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }


    // Control Samba4.service Status == Tab 3 ==

    function tab3(){
        var form = new FormData();
        request(API('return_samba_service_status'), form, function(response) {
            message = JSON.parse(response)["message"];
            if(message == true){
                isActiveButton = '<button type="button" class="btn btn-success" disabled>Samba Servisi Aktif !</button>' ;
                $('#tab3').html(isActiveButton);

                var d1 = document.getElementById('tab2');
                d1.insertAdjacentHTML('beforeend', '<pre id="sambaLog">   </pre>');
                sambaLog();
            } else{
                isActiveButton = '<button type="button" class="btn btn-danger" disabled>Samba Servisi Aktif Değil !</button>' ;
                $('#tab3').html(isActiveButton);

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
            $('#sambaLog').html(message);
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }


    function seizeTheRole(contraction){
        var form = new FormData();
        form.append("contraction",temp);
        
        request(API('seizeTheRole'), form, function(response) {
            message = JSON.parse(response)["message"];
            
            tab1();
            showSwal(message, 'success', 5000); 
            
            
        }, function(error) {
            showSwal(error.message, 'error', 5000);
        });
    }

    /**
     * Showing the servers which have trusted relation with this server
    */

    function trustedServers(){
        var form = new FormData();
        request(API('trusted_servers'), form, function(response) {
            $('#trustedServers').html(response).find('table').DataTable({
            bFilter: true,
            "language" : {
                url : "/turkce.json"
            }
          });;
        }, function(error) {
            $('#trustedServers').html("Hata oluştu");
        });
        
    }

    function showTrustedServerDetailsModal(line){
        var name = line.querySelector("#name").innerHTML;
        var type = line.querySelector("#type").innerHTML;
        var transitive = line.querySelector("#transitive").innerHTML;
        var direction = line.querySelector("#direction").innerHTML;
        console.log(name);
        if(name)
            $('#trustedServerDetailsModal h4.modal-title').html("Details");
        $('#trustedServerDetailsModal').find('.modal-body').html(
            "Name".bold() + "</br>" + name + "</br>" + "</br>" +
            "Type".bold() + "</br>" + type + "</br>" + "</br>" +
            "Transitive".bold() + "</br>" + transitive + "</br>" + "</br>" +
            "Direction".bold() + "</br>" + direction + "</br>" + "</br>"
        );
        $('#trustedServerDetailsModal').modal("show");
    }

    function closeTrustedServerDetailsModal(){
        $('#trustedServerDetailsModal').modal("hide");
    }

    /**
     * Deleting the servers which have trusted relation with this server
    */

    function closeDeleteTrustedServerModal(){
        $('#deleteTrustedServerModal').modal("hide");
    }

    function showDeleteTrustedServerModal(line){
        let name = line.querySelector("#name").innerHTML;
        domainName = name;
        $('#deleteTrustedServerModal').find('.modal-body').prepend(
            "If you destroy trust relation with \"".bold() + name.bold() + "\", please fill the password field.".bold() + "<br><br>");
        $('#deleteTrustedServerModal').find('.modal-footer')
            .append('<button type="button" class="btn btn-primary" onClick="closeDeleteTrustedServerModal()">Cancel</button>');
        $('#deleteTrustedServerModal').modal("show");
    }

    function destroyTrustRelation(){
        var form = new FormData();
        form.append("name", domainName);
        passwd = $('#deleteTrustedServerModal').find('input[name=password]').val();
        form.append("password", passwd);
        closeDeleteTrustedServerModal();
        trustedServers();
        request(API('destroy_trust_relation'), form, function(response) {
            message = JSON.parse(response)["message"];
            showSwal(message, 'success', 3000);
        }, function(error) {
            showSwal(error.message, 'error', 3000);
        });
    }

    /**
     * Creating trust relation with a new server
    */

    function showCreateTrustRelationModal(){
        $('#createTrustRelationModal').modal("show");
    }

    function createTrustRelation(){
        var form = new FormData();
        form.append("newDomainName", $('#createTrustRelationModal').find('input[name=newDomainName]').val());
        form.append("newIpAddr", $('#createTrustRelationModal').find('input[name=newIpAddr]').val());
        form.append("newType", $('#createTrustRelationModal').find('select[name=newType]').val());
        form.append("newDirection", $('#createTrustRelationModal').find('select[name=newDirection]').val());
        form.append("newCreateLocation", $('#createTrustRelationModal').find('select[name=newCreateLocation]').val());
        form.append("newUsername", $('#createTrustRelationModal').find('input[name=newUsername]').val());
        form.append("password", $('#createTrustRelationModal').find('input[name=password]').val());

        $('#createTrustRelationModal').modal("hide");
        request(API('create_trust_relation'), form, function(response) {
            message = JSON.parse(response)["message"];
            showSwal(message, 'success', 10000);
            trustedServers();
        }, function(error) {
            showSwal(error.message, 'error', 3000);
        });
    }

    function replicationInfo(){
        showSwal('{{__("Yükleniyor...")}}','info',2000);
        var form = new FormData();

        request(API('replication_organized'), form, function(response) {
            $('.replicationTable').html(response).find('table').DataTable({
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

    function updateReplication(line) {
        var form = new FormData();

        let inHost = line.querySelector("#hostNameTo").innerHTML;
        let info = line.querySelector("#info").innerHTML;
        let outHost = line.querySelector("#hostNameFrom").innerHTML;

        form.append("inHost", inHost);
        form.append("info", info);
        form.append("outHost", outHost);

        request(API('create_bound'), form, function(response) {
            message = JSON.parse(response)["message"];
            showSwal(message, 'success', 3000);
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function showUpdateTime(line) {
        var form = new FormData();

        let lastUpdateTime = line.querySelector("#lastUpdateTime").innerHTML;   

        form.append("lastUpdateTime", lastUpdateTime);

        request(API('show_update_time'), form, function(response) {
            message = JSON.parse(response)["message"];
            console.log(message);
            showSwal(message, 'success', 3000);
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    
</script>