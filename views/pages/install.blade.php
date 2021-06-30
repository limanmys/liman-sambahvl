@component('modal-component',[
    "id" => "packageInstallerModal",
    "title" => "Görev İşleniyor",
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
<button class="btn btn-success mb-2" id="install" onclick="installSmbPackage()" style="float:left;">{{__('SambaHVL Paketini Kur')}} </button>
<button class="btn btn-danger mb-2" id="delete" style="float:left;margin-left:10px;visibility:hidden;"></button>

<br>
<br>
<br>

<div id="nestedList" class="row" style="display:none;">
    <div class="col-sm-6">
        <div class="card">
        <div class="card-body">
            @include('pages.domain')

        </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card">
        <div class="card-body">
            @include('pages.migration')
        </div>
        </div>
    </div>
</div>

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
    function installSmbPackage(){
        var form = new FormData();
        showSwal('{{__("Yükleniyor...")}}','info',2000);
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
        showSwal('{{__("Kurulum başarıyla tamamlandı.")}}', 'success', 2000);
        setTimeout(function(){
          $('#packageInstallerModal').modal("hide"); 
          window.location.reload();
        }, 2000);
    }
    function onTaskFail(){
        showSwal('{{__("Kurulum sırasında bir hata ile karşılaşıldı.")}}!', 'error', 2000);
    }
</script>