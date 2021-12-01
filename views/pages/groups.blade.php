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

                function updateGroupTable(){
                    let data = new FormData();
                    data.append("groupDN", $("#addUserToGroupGroup").val());     
                    request(API('get_group_members'), data, function(response) {
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

                function showGroupMembers(node){
                    showSwal("{{__('Yükleniyor...')}}", 'info');


                    $("#addUserToGroupGroup").val($(node).find("#dn").text());

                    updateGroupTable();
                }

            
                function closeMembersModal(){
                    $('#groupMembersModal').modal('hide');
                    $('.modal-backdrop').remove();
                    $("#addUserToGroupGroup").val(null);
                }

                function showAddUserToGroupModal(){
                    $("#addUserToGroupModal").modal('show');
                }
           
           
                function addUserToGroup(){
                    let user = $("#addUserToGroupUser").val();
                    let group = $("#addUserToGroupGroup").val();

                    let data = new FormData();
                    data.append("user", user);
                    data.append("group", group);

                    request(API('add_user_to_group'), data, function(response) {                        


                        updateGroupTable();
                        $('#addUserToGroupModal').modal('hide');

                    }, function(error) {
                        showSwal(error.message, 'error', 5000);
                    });
                }
           
           
           </script>