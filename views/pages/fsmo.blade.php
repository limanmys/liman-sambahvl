@component('modal-component',[
        "id" => "infoModal",
        "title" => "Sonuç Bilgisi",
        "footer" => [
            "text" => "OK",
            "class" => "btn-success",
            "onclick" => "hideInfoModal()"
        ]
    ])
@endcomponent

@component('modal-component',[
        "id" => "changeModal",
        "title" => "Rol Seçimi",
        "footer" => [
            "text" => "AL",
            "class" => "btn-success",
            "onclick" => "hideChangeModal()"
        ]
    ])
    @include('inputs', [
        "inputs" => [
            "Roller:newType" => [
                "SchemaMasterRole" => "schema",
                "InfrastructureMasterRole" => "infrastructure",
                "RidAllocationMasterRole" => "rid",
                "PdcEmulationMasterRole" => "pdc",
                "DomainNamingMasterRole" => "naming",
                "DomainDnsZonesMasterRole" => "domaindns",
                "ForestDnsZonesMasterRole" => "forestdns",
                "All" => "all"
            ]
        ]
    ])
@endcomponent

@component('modal-component',[
        "id" => "warningModal",
        "title" => "Uyarı",
        "footer" => [
            "text" => "Evet",
            "class" => "btn-success",
            "onclick" => "warningModalYes()"
        ]
    ])
    
@endcomponent


<p>Tablo üzerinde sağ tuş ile bir rolü üzerinize alabilir veya bunun için butonları kullanabilirsiniz.</p>
<br />
<button class="btn btn-success mb-2" id="btn1" onclick="showInfoModal()" type="button">Tüm rolleri al</button>
<button class="btn btn-success mb-2" id="btn2" onclick="showChangeModal()" type="button">Belirli bir rolü al</button>
<div class="table-responsive" id="fsmoTable"></div>


<script>
    // == Printing Table ==
    function printTable(){
        showSwal('Yükleniyor...','info',2000);
        var form = new FormData();
        request(API('roles_table'), form, function(response) {
            $('#fsmoTable').html(response).find('table').DataTable({
            bFilter: true,
            "language" : {
                url : "/turkce.json"
            }
            });;
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
        
    }

    // == Transfer Role ==
    function takeTheRole(line){
        var form = new FormData();
        let contraction = line.querySelector("#contraction").innerHTML;
        form.append("contraction",contraction);
        request(API('take_the_role'), form, function(response) {
            message = JSON.parse(response)["message"];
            if(message.includes("successful")){
                printTable();
                showSwal(message,'success',7000);
            }
            else if(message.includes("already")){
                showSwal(message,'info',7000);
            }
            else if(message.includes("WERR_HOST_UNREACHABLE")){
                showSwal('WERR_HOST_UNREACHABLE \nTrying to seize... ','info',5000);
                showWarningModal();
                temp=contraction;
            }                
            else{
                showSwal(message, 'error', 7000);
            }
        }, function(error) {
            showSwal(error.message, 'error', 5000);
        });
    }

    // == Information Modal ==
    function showInfoModal(line){
        showSwal('Yükleniyor...','info',3500);
        var form = new FormData();
        request(API('take_all_roles'), form, function(response) {
            message = JSON.parse(response)["message"];
            $('#infoModal').find('.modal-body').html(
                "<pre>"+message+"</pre>"
            );
            $('#infoModal').modal("show");
        }, function(error) {
            showSwal(error.message, 'error', 5000);
        });
    }

    function hideInfoModal(line){
        $('#infoModal').modal("hide");
        printTable();
    }

    // == Change Modal ==
    function showChangeModal(){
        showSwal('Yükleniyor...','info',2000);
        $('#changeModal').modal("show");
    }

    function hideChangeModal(){
        var form = new FormData();
        let contraction = $('#changeModal').find('select[name=newType]').val();
        form.append("contraction",contraction);
        $('#changeModal').modal("hide");
        showSwal('Yükleniyor...','info',5000);

        request(API('take_the_role'), form, function(response) {
            message = JSON.parse(response)["message"];
            if(message.includes("successful")){
                printTable();
                showSwal(message,'success',7000);
            }
            else if(message.includes("already")){
                showSwal(message,'info',7000);
            }
            else if(message.includes("WERR_HOST_UNREACHABLE")){                
                showSwal('WERR_HOST_UNREACHABLE \nTrying to seize... ','info',5000);
                showWarningModal();
            }                
            else{
                showSwal('Hata oluştu.', 'error', 7000);
            }
        }, function(error) {
            $('#changeModal').modal("hide");
            showSwal(error.message, 'error', 5000);
        });
    }

    // == Seize Role ==
    function seizeTheRole(contraction){
        var form = new FormData();
        form.append("contraction",temp);
        
        request(API('seize_the_role'), form, function(response) {
            message = JSON.parse(response)["message"];
            
            printTable();
            showSwal(message, 'success', 5000); 
            
            
        }, function(error) {
            showSwal(error.message, 'error', 5000);
        });
    }

     //== Warning Modal ==
     function showWarningModal(contraction){
        showSwal('Yükleniyor...','info',2000);
        //console.log(contraction);
        $('#warningModal').find('.modal-footer').html(
            '<button type="button" class="btn btn-success" onClick="warningModalYes()">Evet</button> '
            + '<button type="button" class="btn btn-danger" onClick="warningModalNo()">Hayır</button>');
        $('#warningModal').find('.modal-body').html(
            " Rolünü almaya çalıştığınız sunucuya erişilemiyor ! \n Yine de devam etmek ister misiniz ?");
        $('#warningModal').modal("show");
    }

    function warningModalYes(){
        $('#warningModal').modal("hide");
        seizeTheRole(contraction);
    }

    function warningModalNo(){
        showSwal('Yükleniyor...','info',2000);
        $('#warningModal').modal("hide");
    }

</script>