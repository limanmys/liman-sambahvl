<div class="p-3 text-center ">
    <h1 class="mb-3">{{__('Etki Alanı Oluşturma')}}</h1>
</div>
<div class="alert alert-primary d-flex align-items-center " role="alert" id="infoAlert">
    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
    <i class="fas fa-icon mr-2"></i>
    <div>
    {{__('Etki alanı oluşturmak için butonu kullanabilirsiniz.')}}
    </div>
</div>
<br />
<button class="btn btn-success mb-2" id="createDomainButton" onclick="createDomain()" type="button">{{__('Etki Alanı Oluştur')}}</button>
<div id="domainStatus"></div> 
<pre id="createDomainLogs" style="overflow:auto;height:200px"> </pre>



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
        $('#domainStatus').html("<b>{{__('Etki alanı oluşturuluyor. Lütfen bekleyiniz.')}}</b>");
        request(API('create_samba_domain'), form, function(response) {
            //returnDomainInformations();
            observe();
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }

    function observe(){
        var form = new FormData();
        request(API('observe_installation'), form, function(response) {
            message = JSON.parse(response)["message"];
            $("#createDomainLogs").text(message);
            window.setInterval(function() {
                var elem = document.getElementById('createDomainLogs');
                elem.scrollTop = elem.scrollHeight;
            }, 1000);
            setTimeout(() => {
                observe();
            }, 3000);
           
        }, function(response) {
          let error = JSON.parse(response);
           if(error["status"] == 202){
            $('#createDomainLogs').append(error.message);
            refreshAfterLog();
           } else{
            $('#createDomainLogs').append("{{__('Kurulum sırasında hata oluştu.')}}");
           }
        });
    }
    
    function returnDomainInformations(){
        var form = new FormData();
        request(API('return_domain_informations'), form, function(response) {
            message = JSON.parse(response)["message"];
            $('#domainStatus').html("<b>{{__('Etki alanı bilgileri :')}}</b>");
            $('#domainLogs').html("\n" + message);
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }

</script>
