<select name="groups" id="groupType">
    <option value="none" >Tümü</option>
    <option value="security">Security</option>
    <option value="distribution">Distribution</option>
</select>
<button class="btn btn-success mb-2" id="groupBtn" onclick="listGroups()"  type="button">Listele</button>
<br />
<br />
<div class="table-responsive" id="groupsTable"></div>

<script>
    function listGroups(){
        showSwal('Yükleniyor...','info',2000);
        var form = new FormData();
        let e = document.getElementById("groupType");
        var groupType = e.value;
        form.append("groupType",groupType);
        request(API('list_groups'), form, function(response) {
            $('#groupsTable').html(response).find('table').DataTable({
            bFilter: true,
            "language" : {
                url : "/turkce.json"
            }
            });;
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 3000);
        });
    }
</script>