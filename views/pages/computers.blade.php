@if (certificateExists(server()->ip_address, 636))
    @if (isCertificateValid(server()->ip_address, 636))
        @if (ldapCheck(strtolower(extensionDb('domainName')), "administrator", extensionDb('domainPassword'), server()->ip_address, 636))
            
            <div class="alert alert-primary d-flex align-items-center " id="infoDivGroups" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
                <i class="fas fa-icon mr-2"></i>
                <div>
                    {{__('Aşağıdaki tablodan sunucudaki bilgisayarları görebilirsiniz.')}}
                </div>
            </div>
            @include('modal-button', [
                "class" => "btn btn-success mb-2",
                "target_id" => "createComputerModal",
                "text" => "Yeni Bilgisayar"
                ])
            
            @component('modal-component',[
                "id" => "createComputerModal",
                "title" => "Lütfen oluşturmak istediğiniz bilgisayar bilgilerini giriniz",
                
            ]) 
            <form>
                <div class="form-group">
                    <label for="computerNameCreate">{{__('Bilgisayar Adı')}}</label>
                    <input class="form-control" id="computerNameCreate" a" placeholder="{{__('Bilgisayar adı')}}">
                </div>
            </form>
            <button class="btn btn-success" onclick="createComputer()" >{{__('Oluştur')}}</button>
            @endcomponent
			
            <div class="table-responsive" id="computersTable"></div>

            <script>

                function listComputers(){
                    var form = new FormData();
                    request(API('list_computers'), form, function(response) {
                        $('#computersTable').html(response).find('table').DataTable(dataTablePresets('normal'));
                        Swal.close();
                    }, function(response) {
                        let error = JSON.parse(response);
                        Swal.close();
                        showSwal(error.message, 'error', 3000);
                    });
                }
                
			 function createComputer(){
                    
                    computerName = document.getElementById("computerNameCreate").value;
                    var form = new FormData();
                    form.append("computerName", computerName);

                    request(API('create_computer'), form, function(response) {
                        message = JSON.parse(response)["message"];
                        listComputers();
                        $('#createComputerModal').modal("hide");
                        showSwal(message, 'success', 3000);

                    }, function(response) {
                        let error = JSON.parse(response);
                        showSwal(error.message, 'error', 3000);
                    });
                }

                function deleteComputer(item){  
                    //console.log(item);
                    var form = new FormData();
                    let computerName = item.querySelector("#name").innerHTML;
                    form.append("computerName",computerName);
                    request(API('delete_computer'), form, function(response) {
                        listComputers();
                    }, function(error) {
                            showSwal(error.message, 'error', 5000);
                    });
                }


            </script>
        @else
            <div id="noLDAPDiv3" style="visibility:none;"></div>
            <script>
                function listComputers(){
                $('#noLDAPDiv3').html(
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
        <div id="invalidCertificate3" style="visibility:none;"></div>
        <script>
            function listComputers(){
                $('#invalidCertificate3').html(
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
    <div id="noCertificateDiv3" style="visibility:none;"></div>
    <script>
        function listComputers(){
        $('#noCertificateDiv3').html(
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