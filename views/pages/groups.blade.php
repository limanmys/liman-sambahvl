@if (certificateExists(server()->ip_address, 636))
    @if (isCertificateValid(server()->ip_address, 636))
        
        @if (ldapCheck(strtolower(extensionDb('domainName')), "administrator", extensionDb('domainPassword'), server()->ip_address, 636))

            <div class="alert alert-primary d-flex align-items-center " id="infoDivGroups" role="alert">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
            <i class="fas fa-icon mr-2"></i>
            <div>
                {{__('İstediğiniz grup türünü açılır pencereden seçip buton yardımı ile listeletebilir veya buton yardımıyla yeni bir grup oluşturabilirsiniz.')}}
            </div>
            </div>
            <div id="test">
            @include('inputs', [
                    "inputs" => [
                        "Listelenebilecek Gruplar:groupType" => [
                                "Tümü" => "none",
                                "Security" => "security",
                                "Distribution" => "distribution"
                        ],
                    ]
                ])
            </div>
            <small><button class="btn btn-success mb-2" id="groupBtn" onclick="listGroups()"  type="button">{{__('Listele')}}</button></small>
            
            @include('modal-button', [
                "class" => "btn btn-success mb-2",
                "target_id" => "createGroupModal",
                "text" => "Grup Oluştur"
                ])
            @component('modal-component',[
                "id" => "createGroupModal",
                "title" => "Lütfen oluşturmak istediğiniz grup bilgilerini giriniz",
                
            ]) 
            <form>
                <div class="form-group">
                    <label for="groupnameCreate">{{__('Grup adı')}}</label>
                    <input class="form-control" id="groupnameCreate" aria-describedby="groupnameHelp" placeholder="{{__('Grup adı')}}">
                    <small id="groupnameHelp" class="form-text text-muted">{{__('Oluşturacağınız grup adını giriniz.')}}</small>
                </div>
            </form>
            <button class="btn btn-success" onclick="createGroup()" style="float:right;">{{__('Oluştur')}}</button>
            @endcomponent

            @component("modal-component", [
                "id" => "groupMembersModal",
                "title" => "Grup Üyeleri",
                "footer" => 
                   [
                    "class" => "btn-success",
                    "onclick" => "closeMembersModal()",
                    "text" => "Kapat"
                    ]
                ])    
                <div class="table-responsive" id="membersTable"></div>

            @endcomponent
            <br />
            <br />
            <div class="table-responsive" id="groupsTable"></div>

            <script>

                function createGroup(){

                    groupname = document.getElementById("groupnameCreate").value;
                    var form = new FormData();
                    form.append("groupname", groupname);

                    request(API('create_group'), form, function(response) {
                        message = JSON.parse(response)["message"];
                        listGroups();
                        $('#createGroupModal').modal("hide");

                        showSwal(message, 'success', 3000);

                    }, function(response) {
                        let error = JSON.parse(response);
                        showSwal(error.message, 'error', 3000);
                    });
                }

                function listGroups(){
                    showSwal('{{__("Yükleniyor...")}}','info');
                    var form = new FormData();
                    var groupType = $('#test').find('select[name=groupType]').val();
                    form.append("groupType",groupType);
                    request(API('list_groups'), form, function(response) {
                        $('#groupsTable').html(response).find('table').DataTable(dataTablePresets('normal'));
                        Swal.close();

                    }, function(response) {
                        let error = JSON.parse(response);
                        Swal.close();
                        showSwal(error.message, 'error', 3000);
                    });
                }
                function deleteGroup(line){
                
                var form = new FormData();
                let name = line.querySelector("#name").innerHTML;
                form.append("name",name);
                request(API('delete_group'), form, function(response) {
                    message = JSON.parse(response)["message"];
                    listGroups();
                    showSwal(message,'success', 3000);
                }, function(error) {
                        showSwal(error.message, 'error', 5000);
                   });
            }
            function showGroupMembers(node){
                    showSwal("{{__('Yükleniyor...')}}", 'info');
                     console.log(node);

                    let data = new FormData();
                    data.append("groupDN", $(node).find("#dn").html());     
                    request(API('get_group_members'), data, function(response) {
                        console.log(response);
                        $('#membersTable').html(response).find('table').DataTable(dataTablePresets('normal'));
                        //$('#group-members').html(message);
                        $('#groupMembersModal').modal('show');
                        Swal.close();
                    }, function(response) {
                        let error = JSON.parse(response);
                        Swal.close();
                        showSwal(error.message, 'error', 3000);
                    });
                }

                function closeMembersModal(){
                    $('#groupMembersModal').modal('hide');
                }
            </script>
        @else
            <div id="noLDAPDiv2" style="visibility:none;">
            <div class="alert alert-danger d-flex align-items-center"  role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill" /></svg>
                    <i class="fas fa-icon mr-2"></i>
                    <div>
                        {{__("Hata : LDAP\'a Bağlanılamadı !")}}
                    </div>
                    </div>
            </div>
                
        @endif
    @else
            <div id="invalidCertificate2" style="visibility:none;">
                <div class="alert alert-danger d-flex align-items-center"  role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill" /></svg>
                    <i class="fas fa-icon mr-2"></i>
                    <div>
                        {{__("Hata : Sertifikanız hatalı veya güncel değil !")}}
                    </div>
                    </div>
            </div>
    @endif

@else
    <div id="noCertificateDiv2" style="visibility:none;"></div>
    <script>
    
        $('#noCertificateDiv2').html(
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
        
    
    </script>

@endif