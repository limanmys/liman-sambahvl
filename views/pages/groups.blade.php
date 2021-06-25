<div class="alert alert-primary d-flex align-items-center " id="infoDivGroups" role="alert">
  <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
  <i class="fas fa-icon mr-2"></i>
  <div>
        Istediğiniz grup türünü açılır pencereden seçip buton yardımı ile listeletebilirsiniz.
  </div>
</div>
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