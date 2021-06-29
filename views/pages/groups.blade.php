<div class="alert alert-primary d-flex align-items-center " id="infoDivGroups" role="alert">
  <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
  <i class="fas fa-icon mr-2"></i>
  <div>
        Istediğiniz grup türünü açılır pencereden seçip buton yardımı ile listeletebilirsiniz.
  </div>
</div>
<div id="test">
@include('inputs', [
          "inputs" => [
              "Listelenebilecek Gruplar:groupType" => [
                    "Tümü" => "none",
                    "Security" => "security",
                    "Distribution" => "distribution"
              ],
          ]
      ])
</div>
<small><button class="btn btn-success mb-2" id="groupBtn" onclick="listGroups()"  type="button">Listele</button></small>
<br />
<br />
<div class="table-responsive" id="groupsTable"></div>

<script>
    function listGroups(){
        showSwal('Yükleniyor...','info',2000);
        var form = new FormData();
        var groupType = $('#test').find('select[name=groupType]').val();
        console.log(groupType);
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