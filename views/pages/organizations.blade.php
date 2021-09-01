@if (certificateExists(server()->ip_address, 636))
    @if (isCertificateValid(server()->ip_address, 636))
        @if (ldapCheck(strtolower(extensionDb('domainName')), "administrator", extensionDb('domainPassword'), server()->ip_address, 636))



<div class="row">
    <div class="col-sm-6">
        <div class="card">
        <div class="card-body">
            <div id="organizationsTree"></div>
        </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card">
        <div class="card-body">
            <input class="form-control" type="text" id="tableMessage" readonly>
            <div class="table-responsive objectsTable" id="objectsTable"></div> 
        </div>
        </div>
    </div>
</div>


<script>

let icon_types = {
        "folder" : {
            "icon" : "fa fa-folder"
        },
        "base" : {
            "icon" : "fa fa-server"
        }
    };

setTimeout(() => {
    listOrganizations("{{extensionDb('domainName')}}");
}, 500);

let path = "{{extensionDb('domainName')}}"    

$("#organizationsTree").jstree({
        core :{
            data : {
                "id" : path,
                "text" : path,
                "state" : {"opened": false, "selected": true},  //ilkini açtı kökün altındaki 1.dizinler gösteriliyor
                "type" : "base"
            },
            "check_callback": true
        },
        plugins : ["types", "wholerow", "sort", "grid"],
        /*contextmenu: {
            items: function (item) {
                return {
                    move: {
                        label: 'Taşı',
                        icon : "fas fa-angle-double-right",
                        action: function () {
                            //
                        }
                    },
                }
            },
        },*/
        types : icon_types
    }).on('select_node.jstree',function(event,data){
        path = data["node"]["id"];
        type = data["node"]["type"];
        //console.log($("#fileTree").jstree("get_selected")[0]);    // dizin2ye bas -> /srv/dizin1/dizin2 
            listOrganizations(path); 
    });


let isEmpty = function(str) {
    // This doesn't work the same way as the isEmpty function used 
    // in the first example, it will return true for strings containing only whitespace
    return (str.length === 0 || !str.trim());
};

function listOrganizations(path = null){
    showSwal("Yükleniyor...", 'info');
  
    if(path == null){
        path = $("#organizationsTree").jstree("get_selected")[0];
    }

    let formData = new FormData();
    formData.append("path",path);

    request(API('list_organizations'), formData, function(response){
      //  let data = JSON.parse(response)["message"];
      //console.log(response);
     
        if (!isEmpty(response)){
            let data = JSON.parse(response)["message"];
            let fileTree = $("#organizationsTree").jstree(true); //get instance without creating one
            let selected = fileTree.get_selected()[0]; 
            data.forEach(element => {
                if(!fileTree.get_node(element["id"])){
                    fileTree.create_node(selected,element,"inside",function(){});
                }
            });
            fileTree.sort(selected,true);
            fileTree.open_node(selected,false);
            listObjects(null)
        }
        
        else{
            listObjects(path)
        }
        Swal.close();
    }, function(response){
        response = JSON.parse(response);
        showSwal(response.message, 'error');
    });
}

function listObjects(path = null){
    
    if(path == null){
        $('.objectsTable').html(null).find('table').DataTable(dataTablePresets('normal'));
        document.getElementById("tableMessage").value = "Organizasyon leaf değil!";
    }

    else{
        showSwal("Yükleniyor...", 'info', 1500);
        let formData = new FormData();
        formData.append("path",path);
        request(API('list_objects'), formData, function(response){
            if (isEmpty(response)){
                $('.objectsTable').html(null).find('table').DataTable(dataTablePresets('normal'));
                document.getElementById("tableMessage").value = "Organizasyonun altında obje yok!";
            }
            else{
                $('.objectsTable').html(response).find('table').DataTable(dataTablePresets('normal'));
                document.getElementById("tableMessage").value = "Organizasyonun objeleri bulundu!";
            }
        }, function(response){
            response = JSON.parse(response);
            showSwal(response.message, 'error');
        });
    }
}


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
