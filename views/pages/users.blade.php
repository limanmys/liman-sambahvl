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


            @component('modal-component',[
                "id" => "updateAttributeModal",
                "title" => "Lütfen güncellenecek bilgileri girip kaydedin.",
                
            ]) 
       
            <form onsubmit="return false;">
                <div class="form-group">
                    <label for="updateAttributeKey">{{__('Nitelik')}}</label>
                    <input class="form-control" id="updateAttributeKey" aria-describedby="updateAttributeKey" placeholder="{{__('Nitelik')}}" disabled/>
                </div>
                <div class="form-group">
                    <label for="updateAttributeValue">{{__('Değer')}}</label>
                    <input type="text" class="form-control" id="updateAttributeValue" placeholder="{{__('Değer')}}">
                    <small id="updateAttributeValueHelp" class="form-text text-muted">{{__('Yeni değeri giriniz.')}}</small>
                </div>

                <input id="updateAttributeSamaccountname" type="hidden"/>

                <button class="btn btn-success" onclick="updateAttribute()" style="float:right;">{{__('Güncelle')}}</button>
            </form>
            
            @endcomponent




            <div class="table-responsive" id="usersTable"></div>

            <script>
                listUsers();
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
                    showSwal('{{__("Yükleniyor...")}}','in0fo');
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


                function refreshAttributesTable(){
                    let data = new FormData();
                    data.append("samaccountname", $("#updateAttributeSamaccountname").val());   

                    request(API('get_attributes'), data, function(response) {

                        $('.clickedname').html(response);
                        $('#attributesModal').modal('show');
                        Swal.close();
                    }, function(response) {
                        let error = JSON.parse(response);
                        Swal.close();
                        showSwal(error.message, 'error', 3000);
                    });
                }


                function showAttributes(node){

                    showSwal("{{__('Yükleniyor...')}}", 'info');
                    // console.log(node);


                    $("#updateAttributeSamaccountname").val($(node).find("#samaccountname").text());
                    refreshAttributesTable();


                }

            
                function closeAttrModal(){
                    $('#attributesModal').modal('hide');
                    $("#updateAttributeSamaccountname").val(null);
                }


                function showAttributeUpdateModal(node){
                    $("#updateAttributeKey").val($(node).find("#key").text());
                    $("#updateAttributeValue").val($(node).find("#value").text());

                    refreshAttributesTable();
                    $("#updateAttributeModal").modal('show');
                }


                function updateAttribute(){
                    let data = new FormData();


                    data.append('samaccountname', $("#updateAttributeSamaccountname").val());
                    data.append('key', $("#updateAttributeKey").val());
                    data.append('value', $("#updateAttributeValue").val());



                    request(API('update_attribute'), data, function(response) {
                        console.log(response);                        


                        $('#updateAttributeModal').modal('hide');

                        $('#attributesModal').modal('hide');
                        refreshAttributesTable();
                        $('#attributesModal').modal('show');


                    }, function(error) {
                        error = JSON.parse(error);
                        console.log(error);
                        showSwal(error.message, 'error', 5000);
                    });
                }

            </script>