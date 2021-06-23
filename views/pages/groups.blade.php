<select name="groups" id="groupType">
    <option value="" selected disabled>Please Select</option>
    <option value="none" >None</option>
    <option value="security">Security Groups</option>
    <option value="distribution">Distribution Groups</option>
</select>
<button class="btn btn-success mb-2" onclick="listGroups()"  type="button">Listele</button>
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

        //console.log(selection);
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