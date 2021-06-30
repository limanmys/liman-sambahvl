<div class="alert alert-primary d-flex align-items-center " id="infoDivGroups" role="alert">
  <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
  <i class="fas fa-icon mr-2"></i>
  <div>
    {{__('Istediğiniz grup türünü açılır pencereden seçip buton yardımı ile listeletebilirsiniz.')}}
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
<small><button class="btn btn-success mb-2" id="groupBtn" onclick="listGroups()"  type="button">{{__('Listele')}}</button></small>
<br />
<br />
<div class="table-responsive" id="groupsTable"></div>

<script>
    function listGroups(){
        showSwal('{{__("Yükleniyor...")}}','info');
        var form = new FormData();
        var groupType = $('#test').find('select[name=groupType]').val();
        form.append("groupType",groupType);
        request(API('list_groups'), form, function(response) {
            $('#groupsTable').html(response).find('table').DataTable(dataTablePresets('normal'));
            Swal.close();

        }, function(response) {
            let error = JSON.parse(response);
            Swal.close();
            showSwal(error.message, 'error', 3000);
        });
    }
</script>