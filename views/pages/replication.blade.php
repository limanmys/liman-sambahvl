<div id="replicationPrintArea"></div> 
<div class="table-responsive replicationTable" id="replicationTable"></div> 

<script>
    function replicationInfo(){
        showSwal('{{__("Yükleniyor...")}}','info');
        var form = new FormData();

        request(API('replication_organized'), form, function(response) {
            $('.replicationTable').html(response).find('table').DataTable(dataTablePresets('normal'));
            Swal.close();
        }, function(response) {
            let error = JSON.parse(response);
            Swal.close();
            showSwal(error.message, 'error', 3000);
        });

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
            showSwal(message, 'success', 3000);
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function showUpdateTime(line) {
        var form = new FormData();

        let lastUpdateTime = line.querySelector("#lastUpdateTime").innerHTML;   

        form.append("lastUpdateTime", lastUpdateTime);

        request(API('show_update_time'), form, function(response) {
            message = JSON.parse(response)["message"];
            showSwal(message, 'info', 3000);
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }
</script>
