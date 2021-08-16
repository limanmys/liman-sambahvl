

@if (certificateExists(server()->ip_address, 636))
    @if (isCertificateValid(server()->ip_address, 636))
        @if (ldapCheck(strtolower(extensionDb('domainName')), "administrator", extensionDb('domainPassword'), server()->ip_address, 636))
        
            <div class="alert alert-primary d-flex align-items-center " role="alert" id="infoAlert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
                <i class="fas fa-icon mr-2"></i>
                <div>
                    {{__('Buton yardımıyla yeni bir kullanıcı oluşturabilir veya tablodan mevcut kullanıcıları görebilirsiniz.')}}
                </div>
            </div>
            @include('modal-button', [
                    "class" => "btn btn-success mb-2",
                    "target_id" => "createUserModal",
                    "text" => "Kullanıcı Oluştur"
                    ])

            @component('modal-component',[
                "id" => "createUserModal",
                "title" => "Lütfen oluşturmak istediğiniz kullanıcının bilgilerini giriniz",
                
            ]) 
       
            <form>
                <div class="form-group">
                    <label for="usernameCreate">{{__('Kullanıcı adı')}}</label>
                    <input class="form-control" id="usernameCreate" aria-describedby="usernameHelp" placeholder="{{__('Kullanıcı adı')}}">
                    <small id="usernameHelp" class="form-text text-muted">{{__('Oluşturacağınız kullanıcının adını giriniz.')}}</small>
                </div>
                <div class="form-group">
                    <label for="passwordCreate">{{__('Parola')}}</label>
                    <input type="password" class="form-control" id="passwordCreate" placeholder="{{__('Parola')}}">
                    <small id="passwordHelp" class="form-text text-muted">{{__('Oluşturacağınız kullanıcının parolasını giriniz.')}}</small>
                </div>
            </form>
            <button class="btn btn-success" onclick="createUser()" style="float:right;">{{__('Oluştur')}}</button>
            @endcomponent
            @component("modal-component", [
                "id" => "attributesModal",
                "title" => "Kullanıcı Bilgileri",
                "footer" => 
                   [
                    "class" => "btn-success",
                    "onclick" => "closeAttrModal()",
                    "text" => "Kapat"
                    ]
                ])    
                <div class="clickedname"></div>

            @endcomponent
            @component("modal-component", [
                "id" => "userTreeModal",
                "title" => "",
                "footer" => 
                   [
                    "class" => "btn-primary",
                    "onclick" => "Move()",
                    "text" => "Taşı",
                    ]
                ])    
                <div id="user-area" >
                    <div class="card-body">
                        <h5 class="card-title" id="selected-user-dn"></h5>
                        <!--
                            <br><br>
                            <input class="form-control" type="text" id="selected-userdn">  
                            <br>
                            <button onclick="changeUserdn()" class="btn btn-primary btn-sm">{{__("Güncelle")}}</button>
                        -->    
                    </div>
                    <div class="card-body">
                        <pre id="userTree"></pre>
                        <br>
                    </div>
                </div>
            @endcomponent

            <div class="table-responsive" id="usersTable"></div>

            <script>
                
               
                let icons = {
                    "container" : {
                        "icon" : "fas fa-folder"
                    },
                    "organizationalUnit" : {
                        "icon" : "fas fa-folder"
                    },
                    "base": {
                        "icon" : "fas fa-server"
                    },
                    "computer": {
                        "icon" : "fas fa-desktop"
                    },
                    "person": {
                        "icon" : "fas fa-user"
                    },
                    "user": {
                        "icon" : "fas fa-user"
                    },
                    "group": {
                        "icon" : "fas fa-users"
                    },
                    "file" : {
                        "icon" : "fas fa-file"
                    }
                };


                setTimeout(() => {
                    showUserdnTree("{{extensionDb('domainName')}}");
                }, 500);

                let userbasedn = "{{extensionDb('domainName')}}"
                console.log(userbasedn);
                $("#userTree").jstree({
                        core :{
                            data : {
                                "id" : userbasedn,
                                "text" : userbasedn,
                                "state" : {"opened": false, "selected": true},  //ilkini açtı kökün altındaki 1.dizinler gösteriliyor
                                "type" : "base"
                            },
                            "check_callback": true
                        },
                        plugins : ["contextmenu", "types", "wholerow", "sort", "grid"],
                        types : icons
                    }).on('select_node.jstree',function(event,data){
                        path = data["node"]["id"];
                        type = data["node"]["type"];
                        //console.log($("#fileTree").jstree("get_selected")[0]);    // dizin2ye bas -> /srv/dizin1/dizin2 
                        
                        showUserdnTree(path);
                      
                    });


                function showUserdnTree(path = null){
                    showSwal("Yükleniyor...", 'info');
                
                    if(path == null){
                        path = $("#userTree").jstree("get_selected")[0];
                    }

                    let formData = new FormData();
                    formData.append("path",path);

                    request(API('show_userdn_tree'), formData, function(response){
                        //console.log(response);
                        let data = JSON.parse(response)["message"];  //console.log(message);
                        let fileTree = $("#userTree").jstree(true); //get instance without creating one
                        let selected = fileTree.get_selected()[0]; 
                        console.log(data);
                        data.forEach(element => {
                                if(!fileTree.get_node(element["id"])){
                                    fileTree.create_node(selected,element,"inside",function(){});
                                }
                        });
                       // fileTree.sort(selected,true);
                        fileTree.open_node(selected,false);
                        Swal.close();
                    }, function(response){
                        response = JSON.parse(response);
                        showSwal(response.message, 'error');
                    });
                }
                function Move(){

                    let fileTree = $("#userTree").jstree(true);
                    let selected = fileTree.get_selected()[0]; 
                    //console.log(selected); //seçilenin dni
                    let currentdn = $('#selected-user-dn').text();
                    dnarray = currentdn.split(","); 
                    name = dnarray[0];

                    userdn = name + "," + selected;

                    var data = new FormData();
                    data.append("userdn",userdn);

                    request(API('change_userdn'), data, function(response) {
                        userdn = JSON.parse(response)["message"];
                        $('#selected-user-dn').text(userdn);
                        showUserdnTree();
                        //return userdn;
                       
                    }, function(response) {
                        let error = JSON.parse(response);
                        showSwal(error.message, 'error', 3000);
                    });

                }
                function getUserBaseDN(){
                    var form = new FormData();
                    let samacname = line.querySelector("#samaccountname").innerHTML;
                    form.append("samacname",samacname);

                    request(API('get_userbasedn'), form, function(response) {
                        userdn = JSON.parse(response)["message"];
                        return userdn;
                       
                    }, function(response) {
                        let error = JSON.parse(response);
                        showSwal(error.message, 'error', 3000);
                    });
                }

                function moveUser(line){
                    showSwal('{{__("Yükleniyor...")}}','info');
                    var form = new FormData();
                    let samacname = line.querySelector("#samaccountname").innerHTML;
                    form.append("samacname",samacname);

                    request(API('get_userdn'), form, function(response) {
                        userdn = JSON.parse(response)["message"];
                        //showUserTree(userdn);

                        $('#selected-user-dn').text(userdn);
                        $('#userTreeModal').modal('show');
                        //$('#selected-userdn').val(userdn);
                        Swal.close();
                    }, function(response) {
                        let error = JSON.parse(response);
                        showSwal(error.message, 'error', 3000);
                    });
                    // controllera samaccountnamei gönderip user dni çekicez buraya göndericez
                    // treeyi göstershowUserTree(userdn);
                    // inputa dni yaz 
                    // modalı göster 
                }

                function closeUserTreeModal(){
                $('#userTreeModal').modal('hide');
                }


                function changeUserdn(){
                    showSwal('{{__("Yükleniyor...")}}','info');
                    var data= new FormData();
                    data.append("userdn", $("#selected-userdn").val());

                    request(API('change_userdn'), data, function(response) {
                        userdn = JSON.parse(response)["message"];
                        $('#userTreeModal').modal('show');
                        $('#selected-userdn').val(userdn);
                        //showUserTree(userdn);  
                        Swal.close();
                    }, function(response) {
                        let error = JSON.parse(response);
                        showSwal(error.message, 'error', 3000);
                    });
                    //inputtan dni alıcaz userdn = document.getElementById("selected-userdn").value;
                    //controllera dni gönderip değiştirip bilgileri alıcaz tree formatında 
                    //treeyi göstericez
                }

                function createUser(){

                    username = document.getElementById("usernameCreate").value;
                    password = document.getElementById("passwordCreate").value;
                    var form = new FormData();
                    form.append("username", username);
                    form.append("password", password);

                    request(API('create_user'), form, function(response) {
                        message = JSON.parse(response)["message"];
                        listUsers();

                        $('#createUserModal').modal("hide");

                        showSwal(message, 'success', 3000);

                    }, function(response) {
                        let error = JSON.parse(response);
                        showSwal(error.message, 'error', 3000);
                    });
                }

                function listUsers(){
                    showSwal('{{__("Yükleniyor...")}}','info');
                    var form = new FormData();
                    request(API('list_users'), form, function(response) {
                        $('#usersTable').html(response).find('table').DataTable(dataTablePresets('normal'));
                        Swal.close();
                    }, function(response) {
                        let error = JSON.parse(response);
                        Swal.close();
                        showSwal(error.message, 'error', 3000);
                    });
                }

                function deleteUser(line){
                
                var form = new FormData();
                let name = line.querySelector("#name").innerHTML;
                form.append("name",name);
                request(API('delete_user'), form, function(response) {
                    message = JSON.parse(response)["message"];
                    listUsers();
                    
                    showSwal(message,'success', 3000);
                }, function(error) {
                        showSwal(error.message, 'error', 5000);
                   });
            }
          

            function showAttributes(node){

                showSwal("{{__('Yükleniyor...')}}", 'info');
                // console.log(node);

                let data = new FormData();
                data.append("samaccountname", $(node).find("#samaccountname").html());   

                request(API('get_attributes'), data, function(response) {
                    console.log(response);
                    $('.clickedname').html(response);
                    $('#attributesModal').modal('show');
                    Swal.close();
                }, function(response) {
                    let error = JSON.parse(response);
                    Swal.close();
                    showSwal(error.message, 'error', 3000);
                });
                }

            function closeAttrModal(){
                $('#attributesModal').modal('hide');
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

