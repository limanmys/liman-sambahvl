<br />
<div class="table-responsive" id="computersTable"></div>

<script>

    function listComputers(){
        showSwal('{{__("Yükleniyor...")}}','info');
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

</script>