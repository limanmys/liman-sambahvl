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

            $('#updateAttributeModal').modal('hide');

            $('#attributesModal').modal('hide');
            refreshAttributesTable();
            $('#attributesModal').modal('show');


        }, function(error) {
            error = JSON.parse(error);
            showSwal(error.message, 'error', 5000);
        });
    }

</script>