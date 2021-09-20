@if (certificateExists(server()->ip_address, 636))
    @if (isCertificateValid(server()->ip_address, 636))

        @if (ldapCheck(strtolower(extensionDb('domainName')), "administrator", extensionDb('domainPassword'), server()->ip_address, 636))
            @component('modal-component',[
                    "id" => "createSiteModal",
                    "title" => "Lütfen oluşturmak istediğiniz site ismini yazınız",
                   
                ]) 
            <form>
                <div class="form-group">
                    <label for="sitename">{{__('Site adı')}}</label>
                    <input class="form-control" id="sitename" aria-describedby="sitenameHelp" placeholder="{{__('Site adı')}}">
                    <small id="sitenameHelp" class="form-text text-muted">{{__('Oluşturacağınız site adını giriniz.')}}</small>
                </div>
            </form>
                <button class="btn btn-success" onclick="createSite()" style="float:right;">{{__('Oluştur')}}</button>
            @endcomponent

            @component('modal-component',[
                "id" => "viewServersOfSiteModal"
            ])
            <div id="serversOfSite-table" class="table-content">
                <div class="table-body"> </div>
            </div>
            @endcomponent

            @component('modal-component',[
                "id" => "viewAvailableServersOfSiteModal"
            ])
            <div id="availableServers-table" class="table-content">
                <div class="table-body"> </div>
            </div>
            @endcomponent

            <div class="alert alert-primary d-flex align-items-center " role="alert" id="infoAlert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
                <i class="fas fa-icon mr-2"></i>
                <div>
                    {{__('Buton yardımıyla yeni bir site oluşturabilir veya tabloda sağ tuş kullanarak bazı site işlemleri yapabilirsiniz.')}}
                </div>
            </div>

            @include('modal-button', [
                    "class" => "btn btn-success mb-2",
                    "target_id" => "createSiteModal",
                    "text" => "Site Oluştur"
                    ])
            <button id="back" class="btn btn-danger mb-2" onclick="returnPreviousPage()">Geri</button>
            <div class="table-responsive" id="table3"></div>
            
            <div class="table-responsive" id="servers"></div>

            <script>
                $("servers").hide();
                $("#back").hide()
                function listSites(){
                    var form = new FormData();
                    request(API('list_sites'), form, function(response) {
                        $('#table3').html(response).find('table').DataTable(dataTablePresets('normal'));
                    }, function(response) {
                        let error = JSON.parse(response);
                        Swal.close();
                        showSwal(error.message, 'error', 3000);
                    });
                }

                function createSite(){
                    newSiteName = document.getElementById("sitename").value;
                    $('#createSiteModal').modal("hide");
                    var form = new FormData();
                    form.append("newSiteName", newSiteName);
                    request(API('create_site'), form, function(response) {
                        message = JSON.parse(response)["message"];
                        listSites();
                        showSwal(message, 'success', 3000);
                    }, function(response) {
                        let error = JSON.parse(response);
                        showSwal(error.message, 'error', 3000);
                    });
                }

                function deleteSite(line){
                    var siteName = line.querySelector("#name").innerHTML;
                    var form = new FormData();
                    form.append("siteName", siteName);
                    request(API('delete_site'), form, function(response) {
                        message = JSON.parse(response)["message"];
                        showSwal(message, 'success', 3000);
                        listSites();
                    }, function(response) {
                        let error = JSON.parse(response);
                        showSwal(error.message, 'error', 3000);
                    });
                }

                function showServersOfSite(line){
                    var siteName = line.querySelector("#name").innerHTML;
                    var form = new FormData();
                    form.append("siteName", siteName);
                    request(API('servers_of_site'), form, function(response) {
                        $('#serversOfSite-table').find('.table-body').html(response).find("table").DataTable(dataTablePresets('normal'));
                        $('#viewServersOfSiteModal').find('.modal-header').html('<h4><strong>'+siteName+'</strong> | {{__("Site Sunucuları")}} </h4>');
                        $('#viewServersOfSiteModal').modal("show");
                    }, function(response) {
                        let error = JSON.parse(response);
                        showSwal(error.message, 'error', 3000);
                    });
                }

                function addServerToSite(line){
                    var newSiteName = line.querySelector("#name").innerHTML;
                    var form = new FormData();
                    form.append("newSiteName", newSiteName);
                    request(API('add_server_to_site'), form, function(response) {
                        $('#availableServers-table').find('.table-body').html(response).find("table").DataTable(dataTablePresets('normal'));
                        $('#viewAvailableServersOfSiteModal').find('.modal-header').html('<h4><strong>'+newSiteName+'</strong> | {{__("Site Eklenebilecek Mevcut Sunucular")}} </h4>');
                        $('#viewAvailableServersOfSiteModal').modal("show");
                    }, function(response) {
                        let error = JSON.parse(response);
                        showSwal(error.message, 'error', 3000);
                    });
                }

                function addThisServer(line){
                    var dnOfServer = line.querySelector("#dnOfServer").innerHTML;
                    var newSiteName = line.querySelector("#newSiteName").innerHTML;
                    var form = new FormData();
                    form.append("dnOfServer", dnOfServer);
                    form.append("newSiteName", newSiteName);
                    request(API('add_this_server'), form, function(response) {
                        $('#viewAvailableServersOfSiteModal').modal("hide");
                        message = JSON.parse(response)["message"];
                        showSwal(message, 'success', 3000);
                        listSites();
                    }, function(response) {
                        let error = JSON.parse(response);
                        showSwal(error.message, 'error', 3000);
                    });
                }

                function openSite(line){
                    let siteName = $(line).find("#name").html()
                    let form = new FormData();
                    console.log(siteName)
                    form.append("siteName",siteName);
                    request(API('list_site_servers'), form, function(response) { 
                        $("#table3").slideUp();
                        $('#servers').html(response).find('table').DataTable(dataTablePresets('normal'));
                        $("#servers").slideDown();
                        $("#back").slideDown()
                    }, function(response) {
                        let error = JSON.parse(response);
                        showSwal(error.message, 'error', 3000);
                    });
                }

                function returnPreviousPage(line){
                    $("#back").slideUp()
                    $("#back").hide()
                    $("#servers").hide()
                    $("#table3").slideDown();
                }
            </script>
        @else
            <div id="noLDAPDiv4" style="visibility:none;"></div>
            <script>
                function listSites(){
                    $('#noLDAPDiv4').html(
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
        <div id="invalidCertificate4" style="visibility:none;"></div>
        <script>
            function listSites(){
                $('#invalidCertificate4').html(
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
    <div id="noCertificateDiv4" style="visibility:none;"></div>
    <script>
        function listSites(){
            $('#noCertificateDiv4').html(
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