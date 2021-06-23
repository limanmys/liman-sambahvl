<br />
<div class="table-responsive" id="usersTable"></div>


<script>

    function listUsers(){
        showSwal('Yükleniyor...','info',2000);
        var form = new FormData();
        request(API('list_users'), form, function(response) {
            $('#usersTable').html(response).find('table').DataTable({
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