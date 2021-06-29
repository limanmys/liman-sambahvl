<br />
<div class="table-responsive" id="computersTable"></div>

<script>

    function listComputers(){
        showSwal('Yükleniyor...','info');
        var form = new FormData();
        request(API('list_computers'), form, function(response) {
            $('#computersTable').html(response).find('table').DataTable({
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

</script>