@if (certificateExists(server()->ip_address, 636) && isCertificateValid(server()->ip_address, 636) && ldapCheck(strtolower(extensionDb('domainName')), "administrator", extensionDb('domainPassword'), server()->ip_address, 636))
<div class="row">
    <div class="col-6"> 
        <div id="dcs" class="table-responsive"></div>
    </div>
    <div class="col-6">
        <div id="repls" class="table-responsive">
            <input class="form-control" type="text" id="tableMsg" readonly > </input>
        </div>
    </div>
</div>

@component('modal-component',[
    "id" => "replModal",
    "title" => "İstediğiniz replikasyonu seçiniz.",
    "footer" => [
        "text" => "Replike Et",
        "class" => "btn-success",
        "onclick" => "replicate()"
    ]
])

@include('inputs', [
    "inputs" => [
        "Nereden:fromSrv" => [
        ],

        "Nereye:toSrv" => [
        ],

        "Replikasyon Türü:replType" => [
            "Root" => "Root",
            "ForestDnsZones" => "ForestDnsZones",
            "Configuration" => "Configuration",
            "DomainDnsZones" => "DomainDnsZones",
            "Schema" => "Schema"
        ],
    ]
])
<div class="form-check">
    <input class="form-check-input" type="checkbox" value="" id="FullSync">
    <label class="form-check-label" for="FullSync">
        Full Sync
    </label>
</div>

@endcomponent

<script>
    let from;
    let to;

    function showTables(){
        document.getElementById("tableMsg").value = "{{__('Bağlantılarını görmek için lütfen bir etki alan adı denetleyicisine tıklayın.')}}";
        listDcs();
    }
    function listDcs(){
        let form = new FormData();
        request(API('list_dcs'), form, function(response) {
            $('#dcs').html(response).find('table').DataTable(dataTablePresets('normal'));
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function showRepl(line){
        let form = new FormData();
        form.append("dn",line.querySelector("#name").innerHTML);
        request(API('list_repls'), form, function(response) {
            $('#repls').html(response).find('table').DataTable(dataTablePresets('normal'));
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function replicate(){
        if($("#replModal").find('select[name=fromSrv]').val() != $("#replModal").find('select[name=toSrv]').val()){
            showSwal('{{__("Yükleniyor...")}}','info',2000);
            let form = new FormData();
            form.append("fromServer",$("#replModal").find('select[name=fromSrv]').val());
            form.append("toServer",$("#replModal").find('select[name=toSrv]').val());
            form.append("choice",$("#replModal").find('select[name=replType]').val());
            form.append("synctype",$("#FullSync").is(":checked"));
            request(API('replicate'), form, function(response) {
                message = JSON.parse(response)["message"];
                $("#replModal").modal("hide");
                showSwal(message, 'success', 2000);
            }, function(response) {
                let error = JSON.parse(response);
                showSwal(error.message, 'error', 3000);
            });
        }
        else{
            showSwal("Nereden ve Nereye değerleri farklı olmalıdır !", 'error', 3000);
        }
    }

    function showReplModal(line){
        from = line.querySelector("#fromServer").innerHTML;
        to = line.querySelector("#toServer").innerHTML;
        $('[name=fromSrv]').find('option').remove();
        $('[name=toSrv]').find('option').remove();

        $('[name=fromSrv]').append($("<option>",{ value: from, text: from}));
        $('[name=fromSrv]').append($("<option>",{ value: to, text: to}));

        $('[name=toSrv]').append($("<option>",{ value: to, text: to}));
        $('[name=toSrv]').append($("<option>",{ value: from, text: from}));
        
        $("#replModal").modal("show");
    }
</script>

@else
<script>
    function showTables() {
        //
    }
</script>
    <div class="alert alert-danger" role="alert"> 
        <h4 class="alert-heading">Hata !</h4> 
        <p>Sunucuda bağlantı sertifikası bulunamadı !</p> 
        <hr>     
        <p class="mb-0">
            <a href="/ayarlar/sertifika?hostname={{server()->ip_address}}&port=636">  
            {{__("Buraya tıklayarak sunucunuza sertifika ekleyebilirsiniz.")}}
            </a> 
        </p> 
    </div>
@endif

