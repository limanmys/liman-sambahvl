<button class="btn btn-success mb-2" id="replAll">Tümünü Replike Et</button>

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


<script>
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


</script>