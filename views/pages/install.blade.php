@component('modal-component',[
    "id" => "packageInstallerModal",
    "title" => "Görev İşleniyor",
])
@endcomponent

@component('modal-component',[
    "id" => "removeBind9Modal",
    "title" => "Remove",
    "footer" => [
        "text" => "Evet",
        "class" => "btn-success",
        "onclick" => "removeAccepted()"
    ]
])           
@endcomponent

<div id="errorDiv" style="visibility:none;"></div>
<div id="successDiv" style="visibility:none;"></div>
<div class="alert alert-primary d-flex align-items-center " role="alert" id="infoAlert">
    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
    <i class="fas fa-icon mr-2"></i>
    <div>
    {{__('SambaHVL paketini kurmak için lütfen aşağıdaki butonu kullanabilirsiniz.')}} 
    </div>
</div>
<button class="btn btn-block btn-success mb-2" id="install" onclick="checkBind9()" style="padding: .5rem 1rem;
    font-size: 1.25rem;
    line-height: 1.5;
    border-radius: .3rem;"><i class="fas fa-box-open mr-1"></i> {{__('SambaHVL Paketini Kur')}} </button>
<button class="btn btn-danger mb-2" id="delete" style="float:left;margin-left:10px;visibility:hidden;"></button>

<div id="nestedList" class="row border-between mt-4" style="display:none; ">
    <div class="col-sm-6">
        @include('pages.domain')
    </div>
    <div class="col-sm-6">
        @include('pages.migration')
    </div>
    <pre id="sambaHvlLogs" 
        class="mx-2 mt-4"
        style="
        border-radius: 5px;
        background-color: black;
        color: white;
        font-size: medium; 
        font-family: Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;
        width: 100%;
        display: none;
        overflow:auto;
        height: 200px;">
    </pre>
</div>

<style>
.border-between > [class*='col-']:before {
   background: #e3e3e3;
   bottom: 0;
   content: " ";
   left: 0;
   position: absolute;
   width: 1px;
   top: 0;
}

.border-between > [class*='col-']:first-child:before {
   display: none;
}
</style>

<script>

// Install SambaHvl Package == Tab 1 == 
    function tab1(){
        var form = new FormData();
        request(API('verify_installation'), form, function(response) { 
            message = JSON.parse(response)["message"];
            let x = document.getElementById("install");
            if(message == true){
                afterInstallationSteps();
            } else{
                x.disabled = false;
            }
        }, function(error) {
            removeSambaPackageSteps();
        });
    }

    function checkBind9(){

        var form = new FormData();
        showSwal('{{__("Yükleniyor...")}}','info',2000);
        let x = document.getElementById("install");
        x.disabled = true;
        request(API('check_bind9'), new FormData(), function (response) {
            message = JSON.parse(response)["message"];
            if(message){
                showSwal('{{__("Sunucuda bind9 paketi tespit edildi! Paketi kaldırmadan devam edemezsiniz.")}}','info', 3000);
                $('#removeBind9Modal').find('.modal-footer').html(
                    '<button type="button" class="btn btn-success" onClick="removeBind9Accepted()">{{__("Evet")}}</button> '
                    + '<button type="button" class="btn btn-danger" onClick="removeBind9Rejected()">{{__("Hayır")}}</button>');
                    $('#removeBind9Modal').find('.modal-body').html('{{__("bind9 paketini kaldırmadan eklentiyi kullanmaya devam edemezsiniz. Kaldırmak istediğinize emin misiniz? Bu işlem geri alınamayacaktır.")}}');
                    $('#removeBind9Modal').modal("show");
            }
            else{
                showSwal('{{__("Sunucuda bind9 paketi tespit edilmedi. Devam ediliyor.")}}','info',3000);
                installSmbPackage();
            }
        }, function(error) {
            showSwal(error.message, 'error', 3000);
        });
    }

    function removeBind9Accepted(){

        showSwal('{{__("bind9 kaldırılıyor...")}}','info', 2000);
        $('#removeBind9Modal').modal("hide");

        var form = new FormData();
        request(API('remove_bind9'), form, function(response) { 
            message = JSON.parse(response)["message"];
            showSwal('{{__("bind9 başarıyla kaldırıldı. Eklentiyi kullanmaya devam edebilirsiniz.")}}','info', 3000);
            let x = document.getElementById("install");
            x.disabled = false;
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }

    function removeBind9Rejected(){

        showSwal('{{__("bind9 kaldırılmadı, geri dönülüyor...")}}','info', 2000);
        $('#removeBind9Modal').modal("hide");
        let x = document.getElementById("install");
        x.disabled = false;
    }

    function installSmbPackage(){

        var form = new FormData();
        request(API('install_smb_package'), new FormData(), function (response) {
            const output = JSON.parse(response).message;
            $("#install").attr("disabled","true");
            $('#packageInstallerModal').modal({backdrop: 'static', keyboard: false})
            $('#packageInstallerModal').find('.modal-body').html(output);
            $('#packageInstallerModal').modal("show"); 
        }, function(response){
            const error = JSON.parse(response).message;
            showSwal(error,'error',2000);
      })
    }
    function afterInstallationSteps(){
        let installButton = document.getElementById("install");
        installButton.remove();
        let deleteButton = document.getElementById("delete");
        deleteButton.remove();
        let infoAlert = document.getElementById("infoAlert");
        infoAlert.remove();
        $('#successDiv').html(
            '<div class="alert alert-success d-flex align-items-center" role="alert">' +
                '<svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>' +
                '<i class="fas fa-icon mr-2"></i>' +
                '<div>'+
                    '{{__("Sunucuda SambaHvl paketi tespit edildi !")}}'+
                '</div>'+
            '</div>');
        
        let navBar = document.getElementById("nestedList");
        navBar.style.display = null;
    }
    function removeSambaPackage(){
        var form = new FormData();
        showSwal('{{__("Samba paketi kaldırılıyor.")}}', 'info', 3000);
        request(API('delete_smb_package'), form, function(response) {
            showSwal('{{__("Paket başarıyla kaldırıldı !")}}', 'success', 3000);
            window.location.reload();
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }
    function removeSambaPackageSteps(){
        let installButton = document.getElementById("install");
        installButton.disabled = true;
        let infoAlert = document.getElementById("infoAlert");
        infoAlert.remove();
        $('#errorDiv').html(
            '<div class="alert alert-danger d-flex align-items-center"  role="alert">' +
                '<svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill" /></svg>' +
                '<i class="fas fa-icon mr-2"></i>' +
                '<div>'+
                    '{{__("Hata : Sunucuda kurulu Samba Paketi tespit edildi !")}}'+
                '</div>'+
            '</div>');
            
        let deleteButton = document.getElementById("delete");
        deleteButton.onclick = function() {removeSambaPackage()};
        deleteButton.innerText = '{{__("Samba Paketini Kaldır")}}';
        deleteButton.style.visibility = "visible";
    }
    function onTaskSuccess(){
        var form = new FormData();

        request(API('check_installation'), form, function(response) {
            showSwal('{{__("Kurulum başarıyla tamamlandı.")}}', 'success', 2000);
            setTimeout(function(){
                    $('#packageInstallerModal').modal("hide"); 
                    window.location.reload();
                    }, 2000);

        }, function(error) {
            showSwal('{{__("Kurulum tamamlanamadı, depoya erişilemiyor !")}}', 'error', 2000);
            setTimeout(function(){
                    $('#packageInstallerModal').modal("hide"); 
                    window.location.reload();
                    }, 4000);
        });

        
    }
    function onTaskFail(){
        showSwal('{{__("Kurulum sırasında bir hata ile karşılaşıldı.")}}!', 'error', 2000);
    }
</script>