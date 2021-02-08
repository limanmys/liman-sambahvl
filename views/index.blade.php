<button class="btn btn-primary mb-2" onclick="observeInstallation()">SambaHVL Paketini Kur</button>
<div id="smbinstall"></div>

<script>
    function observeInstallation(){
        var form = new FormData();
        request(API('observeInstallation'), form, function(response) {
            message = JSON.parse(response)["message"];
            $('#smbinstall').html(message);
        }, function(error) {
            $('#smbinstall').html("Hata oluştu");
        });
    }
</script>

