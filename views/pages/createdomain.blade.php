<p>Etki alanı kurmak için lütfen aşağıdaki butonu kullanın.</p>
<button class="btn btn-success mb-2" id="createDomainButton" onclick="createDomain()" type="button">Etki Alanı Oluştur</button>
<div id="domainStatus"></div> 
<pre id="domainLogs" class="tab-pane">    
</pre>


<script>
function createDomain(){
    var form = new FormData();
    $('#domainStatus').html("<b>Etki alanı oluşturuluyor. Lütfen bekleyiniz.</b>");
    request(API('create_samba_domain'), form, function(response) {
        returnDomainInformations();
    }, function(error) {
        showSwal(error.message, 'error', 3000);
        
    });
}
    

function returnDomainInformations(){
    var form = new FormData();
    request(API('return_domain_informations'), form, function(response) {
        message = JSON.parse(response)["message"];
        $('#domainStatus').html("<b>Etki alanı bilgileri :</b>");
        $('#domainLogs').html("\n" + message);
    }, function(error) {
        showSwal(error.message, 'error', 3000);
        console.log(error);
    });
}

</script>