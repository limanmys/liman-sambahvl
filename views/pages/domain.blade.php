<div class="p-3 text-center ">
    <h1 class="mb-3">Etki Alanı Oluşturma</h1>
</div>
<div class="alert alert-primary d-flex align-items-center " role="alert" id="infoAlert">
    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
    <i class="fas fa-icon mr-2"></i>
    <div>
    Etki alanı kurmak için aşağıdaki butonu kullanabilirsiniz.
    </div>
</div>
<br />
<button class="btn btn-success mb-2" id="createDomainButton" onclick="createDomain()" type="button">Etki Alanı Oluştur</button>
<div id="domainStatus"></div> 
<pre id="domainLogs" class="tab-pane"></pre>

<script>

// Create New Domain == Tab 2 ==

    function tab2(){
        var form = new FormData();
        request(API('verify_domain'), form, function(response) {
            message = JSON.parse(response)["message"];
            let x = document.getElementById("createDomainButton");
            if(message == true){
                x.disabled = true;
                returnDomainInformations();
            } else{
                x.disabled = false;
            }
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }

    

    function createDomain(){
        var form = new FormData();
        $('#domainStatus').html("<b>Etki alanı oluşturuluyor. Lütfen bekleyiniz.</b>");
        request(API('create_samba_domain'), form, function(response) {
            returnDomainInformations();
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
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
