<div class="p-3 text-center ">
    <h2 class="mb-3"
        style="
        text-transform: uppercase;
        font-weight: 700;"
    >{{__('Etki Alanı Oluşturma')}}</h2>
    <p>
    {{__('Etki alanı oluşturmak için butonu kullanabilirsiniz.')}}
    </p>
</div>
<div class="mycontainer" style="width: 100%; display: flex; align-items:center; justify-content: center;">
    <button class="btn btn-success mb-2" id="createDomainButton" onclick="createDomain()" type="button" style="padding: .5rem 2rem;
        font-size: 1.25rem;
        line-height: 1.5;
        border-radius: .3rem;"><i class="fas fa-network-wired mr-2"></i>{{__('Etki Alanı Oluştur')}}</button>
</div>
<div id="domainStatus"></div> 

<script>
    function tab2(){
        var form = new FormData();
        request(API('verify_domain'), form, function(response) {
            message = JSON.parse(response)["message"];
            let x = document.getElementById("createDomainButton");
            let y = document.getElementById("site");
            if(message == true){
                x.disabled = true;
                y.disabled = true;
                returnDomainInformations();
            } else{
                x.disabled = false;
                y.disabled = false;
            }
        }, function(error) {
            showSwal(error.message, 'error', 3000);
        });
    }

    function createDomain(){
        var form = new FormData();
        let x = document.getElementById("createDomainButton");
        let y = document.getElementById("site");
        x.disabled = true;
        y.disabled = true;
        $('#domainStatus').html("<b>{{__('Etki alanı oluşturuluyor. Lütfen bekleyiniz.')}}</b>");
        request(API('create_samba_domain'), form, function(response) {
            //returnDomainInformations();
            observe();
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            x.disabled = false;
            y.disabled = false;
        });
    }

    function observe(){
        var form = new FormData();
        $("#sambaHvlLogs").css("display", "inline-block");
        request(API('observe_installation'), form, function(response) {
            message = JSON.parse(response)["message"];
            $("#sambaHvlLogs").text(message);
            window.setInterval(function() {
                var elem = document.getElementById('sambaHvlLogs');
                elem.scrollTop = elem.scrollHeight;
            }, 1000);
            setTimeout(() => {
                observe();
            }, 3000);
           
        }, function(response) {
          let error = JSON.parse(response);
           if(error["status"] == 202){
            $('#sambaHvlLogs').append(error.message);
            refreshAfterLog();
           } else{
            $('#sambaHvlLogs').append("{{__('Kurulum sırasında hata oluştu.')}}");
           }
           let x = document.getElementById("createDomainButton");
            let y = document.getElementById("site");
            x.disabled = false;
            y.disabled = false;
        });
    }
    
    function returnDomainInformations(){
        let x = document.getElementById("createDomainButton");
        let y = document.getElementById("site");
        x.disabled = true;
        y.disabled = true;
        var form = new FormData();
        request(API('return_domain_informations'), form, function(response) {
            message = JSON.parse(response)["message"];
            $('#domainStatus').html("<b>{{__('Etki alanı bilgileri :')}}</b>");
            $('#domainLogs').html("\n" + message);
        }, function(error) {
            showSwal(error.message, 'error', 3000);
        });
    }

</script>
