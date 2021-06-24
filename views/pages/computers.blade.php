<br />
<div class="table-responsive" id="computersTable"></div>

<script>

    function listComputers(){
        showSwal('Yükleniyor...','info',2000);
        var form = new FormData();
        request(API('list_computers'), form, function(response) {
            $('#computersTable').html(response).find('table').DataTable({
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

</script>