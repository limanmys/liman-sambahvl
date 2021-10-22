<div class="alert alert-primary d-flex align-items-center " id="infoDivGroups" role="alert">
    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
    <i class="fas fa-icon mr-2"></i>
    <div>
        {{__('Aşağıdaki tabloda replikasyonları görüntüleyebilir, tablo üzerinde sağ tuş ile bir replikasyonu güncelleyebilir veya son güncelleme zamanını öğrenebilirsiniz. Ayrıca buton ile tümünü satırları da replike edebilirsiniz.')}}
    </div>
</div>

<div id="replicationPrintArea"></div> 
<button class="btn btn-success mb-2" id="replicateAllButton" onclick="replicateAll()" type="button">{{__('Tümünü replike et')}}</button>
<div class="table-responsive replicationTable" id="replicationTable"></div> 

<script>
    function replicationInfo(){
        showSwal('{{__("Yükleniyor...")}}','info');
        var form = new FormData();
        let x = document.getElementById("replicateAllButton");
        x.disabled = true;
        request(API('replication_organized'), form, function(response) {
            $('.replicationTable').html(response).find('table').DataTable(dataTablePresets('normal'));
            Swal.close();
            x.disabled = false;
        }, function(response) {
            let error = JSON.parse(response);
            Swal.close();
            showSwal(error.message, 'error', 3000);
        });
    }

    function replicateAll(){

        var table = document.getElementById("replicationTable").getElementsByClassName("tableRow");
        var length = table.length;
        showSwal("İşlem devam ediyor...", 'info');
        for(var i = 0; i < length; i++){
            updateReplication(table[i]);
        }
        Swal.close();
        showSwal('{{__("Başarılı")}}', 'success', 3000);
    }

    function updateReplication(line) {
        var form = new FormData();

        let inHost = line.querySelector("#hostNameTo").innerHTML;
        let info = line.querySelector("#info").innerHTML;
        let outHost = line.querySelector("#hostNameFrom").innerHTML;

        form.append("inHost", inHost);
        form.append("info", info);
        form.append("outHost", outHost);

        request(API('create_bound'), form, function(response) {
            message = JSON.parse(response)["message"];
            showSwal('{{__("Başarılı")}}', 'success', 3000);
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function showUpdateTime(line) {

        let lastUpdateTime = line.querySelector("#lastUpdateTime").innerHTML;   
        showSwal(lastUpdateTime, 'info', 3000);
    }
</script>
