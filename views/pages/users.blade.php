<br />
<div class="table-responsive" id="usersTable"></div>


<script>

    function listUsers(){
        showSwal('Yükleniyor...','info');
        var form = new FormData();
        request(API('list_users'), form, function(response) {
            $('#usersTable').html(response).find('table').DataTable({
            bFilter: true,
            "language" : {
                url : "/turkce.json"
            }
            });;
            Swal.close();
        }, function(response) {
            let error = JSON.parse(response);
            Swal.close();
            showSwal(error.message, 'error', 3000);
        });
    }

</script>