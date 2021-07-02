<button class="btn btn-success mb-2" id="ntp" onclick="timeUpdate()" type="button">Tarih ve Saat Güncelle</button>
<div class="row">
    <div class="col-sm-6">
        <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{__("Sistem Saati")}}</h5>
            <br><br>
            <input class="form-control" type="text" id="systemClock" readonly>
            <br>
            <button onclick="getSystemClock()" class="btn btn-primary">{{__("Sistem Saatini Bul")}}</button>
        </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{__("Donanım Saati")}}</h5>
            <br><br>
            <input class="form-control" type="text" id="hwClock" readonly>
            <br>
            <button onclick="getHardwareClock()" class="btn btn-primary">{{__("Donanım Saatini Bul")}}</button>
        </div>
        </div>
    </div>
</div>

<script>

function timeUpdate(){

        showSwal('{{__("Güncelleniyor...")}}','info',2000);
        var form = new FormData();
        request(API('time_update'), form, function(response) {
            message = JSON.parse(response)["message"];
            getClocks();
            showSwal(message, 'success', 3000);
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }

    function getSystemClock(){
        var form = new FormData();
        request(API('get_system_clock'), form, function(response) {
            message = JSON.parse(response)["message"];
            document.getElementById("systemClock").value = message;
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }

    function getHardwareClock(){
        var form = new FormData();
        request(API('get_hardware_clock'), form, function(response) {
            message = JSON.parse(response)["message"];
            document.getElementById("hwClock").value = message;
        }, function(error) {
            showSwal(error.message, 'error', 3000);
            console.log(error);
        });
    }

    function getClocks(){
        getSystemClock();
        getHardwareClock();
    }

</script>