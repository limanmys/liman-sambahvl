

@if (certificateExists(server()->ip_address, 636))
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
                <small id="usernameHelp" class="form-text text-muted">{{__('Oluşturacağınız kullanıcının adını giriniz.')}}.</small>
            </div>
            <div class="form-group">
                <label for="passwordCreate">{{__('Parola')}}</label>
                <input type="password" class="form-control" id="passwordCreate" placeholder="{{__('Parola')}}">
                <small id="passwordHelp" class="form-text text-muted">{{__('Oluşturacağınız kullanıcının parolasını giriniz.')}}</small>
            </div>
         </form>
        <button class="btn btn-success" onclick="createUser()" style="float:right;">{{__('Oluştur')}}</button>
        @endcomponent
        <div class="table-responsive" id="usersTable"></div>

        <script>
            function createUser(){

                $('#createUserModal').modal("hide");
                let username = $('#createUserModal').find('input[name=usernameCreate]').val();
                let password = $('#createUserModal').find('input[name=passwordCreate]').val();

                var form = new FormData();
                form.append("username", username);
                form.append("password", password);

                request(API('create_user'), form, function(response) {
                    message = JSON.parse(response)["message"];
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

