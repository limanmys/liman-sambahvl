@if (certificateExists(server()->ip_address, 636))
    @if (ldapCheck(strtolower(extensionDb('domainName')), "administrator", extensionDb('domainPassword'), server()->ip_address, 636))
    
        <br />
        <div class="table-responsive" id="usersTable"></div>
        <script>

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

