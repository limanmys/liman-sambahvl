<div class="row">
    <div class="col-12 mb-2">
    <div class="card-body">
            <h5 class="card-title">{{__("DNS Forward")}}</h5>
            <br><br>
            <input class="form-control" type="text" id="dnsForward">
            <br>
            <button onclick="changednsForward()" class="btn btn-primary">{{__("Güncelle")}}</button>
    </div>
    <b>Configuration File:</b>
    <pre class="conf"></pre>
    </div>
</div>
<script>
    function showConfig(){  
        dnsForward();
        var form = new FormData();
        request(API('show_config'), form, function(response) {
            $('.conf').html(response);
           // console.log(response);
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }
    function dnsForward(){

        var form = new FormData();
        request(API('get_dnsForwarder'), form, function(response) {
            $('#dnsForward').val(response);
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }

    function changednsForward(){

        var data = new FormData();
        data.append("dnsForwardData", $("#dnsForward").val());

        request(API('change_DNSForwarder'), data, function(response) {
            showConfig();
            //$('#dnsForward').val(response);
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }

</script>