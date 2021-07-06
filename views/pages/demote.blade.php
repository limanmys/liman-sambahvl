<p>Tablodan sağ tık ile istediğiniz Domain Controller'ı veya butonu kullanarak eklentiyi kullandığınız Domain Controller'ı Demote edebilirsiniz.</p>
<button class="btn btn-danger mb-2" id="domain" onclick="demoteYourself()" type="button">Bu DC'yi Demote Et</button>

<div class="table-responsive" id="demotableTable"></div>

@component('modal-component',[
        "id" => "configureAskModal",
        "title" => "ERROR",
        "footer" => [
            "text" => "Evet",
            "class" => "btn-success",
            "onclick" => "configureAskModalYes()"
        ]
    ])
    
@endcomponent

@component('modal-component',[
        "id" => "demoteConfirmationModal",
        "title" => "Dikkat!",
        "footer" => [
            "text" => "Evet",
            "class" => "btn-success",
            "onclick" => "demoteConfirmationModalYes()"
        ]
    ])
    
@endcomponent

<script>

    function demoteYourself(){

        $('#demoteConfirmationModal').find('.modal-footer').html(
        '<button type="button" class="btn btn-danger" onClick="demoteConfirmationModalYes()">Evet</button> '
        + '<button type="button" class="btn btn-success" onClick="demoteConfirmationModalNo()">Hayır</button>');
        $('#demoteConfirmationModal').find('.modal-body').html("<b><br>Bu işlem geri alınamayacaktır, yine de sunucuyu demote etmek istediğinize emin misiniz?</b>");
        $('#demoteConfirmationModal').modal("show");
    }

    function demoteConfirmationModalYes(){

        $('#demoteConfirmationModal').modal("hide");
        showSwal('{{__("Yükleniyor...")}}','info',4000);
        var form = new FormData();
        request(API('demote_yourself'), form, function(response) {
            message = JSON.parse(response)["message"];
            showSwal(message, 'success', 3000);
            demoted();
        }, function(response) {
            let error = JSON.parse(response);
            if(error.message.includes("ERROR") != false){
                $('#configureAskModal').find('.modal-footer').html(
                '<button type="button" class="btn btn-success" onClick="configureAskModalYes()">Evet</button> '
                + '<button type="button" class="btn btn-danger" onClick="configureAskModalNo()">Hayır</button>');
                $('#configureAskModal').find('.modal-body').html(error.message + "<b><br><br>HATA!!!<br>Demote hatasina ragmen sunucunun domain controlling dosyalarini silmek ister misiniz?</b>");
                $('#configureAskModal').modal("show");
            }
            else{
                showSwal(error.message, 'error', 3000);
            }
        });
    }

    function demoteConfirmationModalNo(){

        showSwal('Yükleniyor...','info',2000);
        $('#demoteConfirmationModal').modal("hide");
    }

    function configureAskModalYes(){

        showSwal('Yükleniyor...','info',2000);
        $('#configureAskModal').modal("hide");
        var form = new FormData();
        request(API('only_configure_documents'), form, function(response) {
            message = JSON.parse(response)["message"];
            showSwal(message, 'success', 3000);
            demoted();
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 5000);
        });
    }

    function configureAskModalNo(){

        showSwal('Yükleniyor...','info',2000);
        $('#configureAskModal').modal("hide");
    }

    function listDemotable(){

        showSwal('{{__("Liste Yükleniyor...")}}','info',2000);
        var form = new FormData();
        request(API('list_demotable'), form, function(response) {
            $('#demotableTable').html(response).find('table').DataTable({
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

    function demoteThisOne(line){

        showSwal('{{__("Yükleniyor...")}}','info',2000);
        var serverName = line.querySelector("#serverName").innerHTML;
        var form = new FormData();
        form.append("serverName", serverName);
        request(API('demote_this_one'), form, function(response) {
            message = JSON.parse(response)["message"];
            showSwal(message, 'success', 3000);
            listDemotable();
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 5000);
        });
    }
</script>




