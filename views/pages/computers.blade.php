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