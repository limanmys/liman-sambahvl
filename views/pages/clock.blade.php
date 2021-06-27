<div class="row">
    <div class="col-sm-6">
        <div class="card">
        <div class="card-body">
            <h5 class="card-title">System Clock</h5>
            <br><br>
            <input class="form-control" type="text" id="systemClock" readonly>
            <br>
            <button onclick="getSystemClock()" class="btn btn-primary">Get System Clock</button>
        </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card">
        <div class="card-body">
            <h5 class="card-title">Hardware Clock</h5>
            <br><br>
            <input class="form-control" type="text" id="hwClock" readonly>
            <br>
            <button onclick="getHardwareClock()" class="btn btn-primary">Get Hardware Clock</button>
        </div>
        </div>
    </div>
</div>

<script>
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