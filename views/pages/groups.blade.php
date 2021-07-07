@if (certificateExists(server()->ip_address, 636))
    @if (ldapCheck(strtolower(extensionDb('domainName')), "administrator", extensionDb('domainPassword'), server()->ip_address, 636))

        <div class="alert alert-primary d-flex align-items-center " id="infoDivGroups" role="alert">
        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
        <i class="fas fa-icon mr-2"></i>
        <div>
            {{__('Istediğiniz grup türünü açılır pencereden seçip buton yardımı ile listeletebilirsiniz.')}}
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
        <br />
        <br />
        <div class="table-responsive" id="groupsTable"></div>

        <script>
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
        </script>
    @else
        <div id="noLDAPDiv2" style="visibility:none;"></div>
        <script>
            
            $('#noLDAPDiv2').html(
                '<div class="alert alert-danger d-flex align-items-center"  role="alert">' +
                '<svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill" /></svg>' +
                '<i class="fas fa-icon mr-2"></i>' +
                '<div>'+
                    '{{__("Hata : LDAP\'a Bağlanılamadı !")}}'+
                '</div>'+
                '</div>'
                );
            
            
        </script>
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