
@if (certificateExists(server()->ip_address, 636))
    @if (isCertificateValid(server()->ip_address, 636))
        @if (ldapCheck(strtolower(extensionDb('domainName')), "administrator", extensionDb('domainPassword'), server()->ip_address, 636))



<div class="row">
    <div class="col-sm-6">
        <div class="card">
        <div class="card-body">
            <div class="table-responsive" id="organizationsTable"></div>
        </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card">
        <div class="card-body">
            <div id="organizationsTree"></div>
        </div>
        </div>
    </div>
</div>


<script>
   let icon_types = {
        "folder" : {
            "icon" : "fas fa-folder"
        },
        "file" : {
            "icon" : "fas fa-file"
        },
        "base": {
            "icon" : "fas fa-server"
        }
    };

function getOrganizations(){
    showSwal("Yükleniyor...", 'info');
    let formData = new FormData();
    let base = "DC=staj,DC=lab";
    formData.append("base", base);
/*
    let path = "{{extensionDb('domainName')}}";
    console.log(path);*/
    
    request(API('get_organizations'), formData, function(response){

        message = JSON.parse(response)["message"];  //console.log(message);
        let data = message;
        $('#organizationsTree').jstree({
            "plugins": [
                "contextmenu","search","types", "wholerow", "sort", "grid"
            ],
            'core': {
                'data': data,
                "check_callback": true
            },
            contextmenu: {
                items: function (item) {
                    return {
                        "Move":  {
                            label: 'Taşı',
                            icon: "fas fa-angle-double-right",
                            action: function () {
                               // moveItem(item.id);
                                console.log(item.id);
                            }
                        },
                    }
                },
            },
            "types" : icon_types
        });
           // $("#organizationsTree").jstree("open_all");
        Swal.close();
    }, function(response){
        response = JSON.parse(response);
        showSwal(response.message, 'error');
    });
}

/*

    function getChildNodes(id){
        showSwal('{{__("Yükleniyor...")}}','info');
        if(id == null){
            id = $("#organizationsTree").jstree("get_selected")[0];
        }
        let formData = new FormData();
        formData.append("nodebase",id);
        request(API('get_child_nodes'), formData, function(response) {
            
            data = JSON.parse(response)["message"]
            let fileTree = $("#organizationsTree").jstree(true); //get instance without creating one
            let selected = fileTree.get_selected()[0]; 
            data.forEach(element => {
                if(!fileTree.get_node(element["id"])){
                    fileTree.create_node(selected,element,"inside",function(){});
                }
            });
           // fileTree.sort(selected,true);
            fileTree.open_node(selected,false);
            Swal.close(); 
        }, function(error) {
            error = JSON.parse(error)["message"]
            showSwal(error,'error');
        });
    }
 */

</script>

@else
            <div id="noLDAPDiv" style="visibility:none;"></div>
                <script>
                    function listUsers(){
                        $('#noLDAPDiv').html(
                            '<div class="alert alert-danger d-flex align-items-center"  role="alert">' +
                            '<svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill" /></svg>' +
                            '<i class="fas fa-icon mr-2"></i>' +
                            '<div>'+
                                '{{__("Hata : LDAP\'a Bağlanılamadı !")}}'+
                            '</div>'+
                            '</div>'
                            );
                    }
                    
                </script>
        @endif
    @else
        <div id="invalidCertificate" style="visibility:none;"></div>
        <script>
            function listUsers(){
                $('#invalidCertificate').html(
                    '<div class="alert alert-danger d-flex align-items-center"  role="alert">' +
                    '<svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill" /></svg>' +
                    '<i class="fas fa-icon mr-2"></i>' +
                    '<div>'+
                        '{{__("Hata : Sertifikanız hatalı veya güncel değil !")}}'+
                    '</div>'+
                    '</div>'
                    );
            }
            
            
        </script>
    @endif
@else
    <div id="noCertificateDiv" style="visibility:none;"></div>
    <script>
    function listUsers(){
        $('#noCertificateDiv').html(
            '<div class="alert alert-danger" role="alert">' +
            '<h4 class="alert-heading">Hata !</h4>' +
            '<p>Sunucuda bağlantı sertifikası bulunamadı !</p>' +
            '<hr>' +
            
            '<p class="mb-0">'+
                '<a href="/ayarlar/sertifika?hostname={{server()->ip_address}}&port=636"> ' +
                '{{__("Buraya tıklayarak sunucunuza sertifika ekleyebilirsiniz.")}}'+
                '</a>' +
            '</p>' +
            '</div>'
            
            );
        }

    
    </script>

@endif

