<div id="errorDivFsmo" style="visibility:none;"></div>
<div class="alert alert-primary d-flex align-items-center " id="infoDivFsmo" role="alert">
  <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
  <i class="fas fa-icon mr-2"></i>
  <div>
  Tablo üzerinde sağ tuş ile bir rolü üzerinize alabilir veya tüm rolleri almak için butonu kullanabilirsiniz.
  </div>
</div>
<button class="btn btn-success mb-2" id="takeallroles_btn" onclick="showInfoModal()" type="button">Tüm rolleri al</button>
<div class="table-responsive" id="fsmoTable"></div>

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
        "id" => "warningModal",
        "title" => "Uyarı",
        "footer" => [
            "text" => "Evet",
            "class" => "btn-success",
            "onclick" => "warningModalYes()"
        ]
    ])
    
@endcomponent


<script>
    // == Printing Table ==
    function printTable(){
        showSwal('Yükleniyor...','info');

        var form = new FormData();
        request(API('roles_table'), form, function(response) {
            $('#fsmoTable').html(response).find('table').DataTable({
            bFilter: true,
            "language" : {
                url : "/turkce.json"
            }
            });;
            Swal.close();
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
                //This DC already has the 'schema' FSMO role
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
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
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
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function hideInfoModal(line){
        $('#infoModal').modal("hide");
        printTable();
    }


    // == Seize Role ==
    function seizeTheRole(contraction){
        var form = new FormData();
        form.append("contraction",temp);
        
        request(API('seize_the_role'), form, function(response) {
            message = JSON.parse(response)["message"];
            
            printTable();
            showSwal(message, 'success', 5000); 
            
            
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000)
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