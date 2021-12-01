@component('modal-component',[
        "id" => "createComputerModal",
        "title" => "Lütfen oluşturmak istediğiniz bilgisayar bilgilerini giriniz",
        
    ]) 
    <form>
        <div class="form-group">
            <label for="computerNameCreate">{{__('Bilgisayar Adı')}}</label>
            <input class="form-control" id="computerNameCreate" a" placeholder="{{__('Bilgisayar adı')}}">
        </div>
    </form>
    <button class="btn btn-success" onclick="createComputer()" >{{__('Oluştur')}}</button>
@endcomponent

@component('modal-component',[
        "id" => "infoModal",
        "title" => "Sonuç Bilgisi",
        "footer" => [
            "text" => "OK",
            "class" => "btn-success",
            "onclick" => "hideInfoModal()"
        ]
    ])
@endcomponent


@component('modal-component',[
        "id" => "warningModal",
        "title" => "Uyarı",
        "footer" => [
            "text" => "Evet",
            "class" => "btn-success",
            "onclick" => "warningModalYes()"
        ]
    ])
    
@endcomponent

@component('modal-component',[
        "id" => "createGroupModal",
        "title" => "Lütfen oluşturmak istediğiniz grup bilgilerini giriniz",
        
    ]) 
    <form>
        <div class="form-group">
            <label for="groupnameCreate">{{__('Grup adı')}}</label>
            <input class="form-control" id="groupnameCreate" aria-describedby="groupnameHelp" placeholder="{{__('Grup adı')}}">
            <small id="groupnameHelp" class="form-text text-muted">{{__('Oluşturacağınız grup adını giriniz.')}}</small>
        </div>
    </form>
    <button class="btn btn-success" onclick="createGroup()" style="float:right;">{{__('Oluştur')}}</button>
@endcomponent

@component("modal-component", [
    "id" => "groupMembersModal",
    "title" => "Grup Üyeleri",
    "footer" => 
        [
        "class" => "btn-success",
        "onclick" => "closeMembersModal()",
        "text" => "Kapat"
        ]
    ])    
    <div class="d-flex justify-content-end">
        <div>
            <button class="btn btn-success my-2" onclick="showAddUserToGroupModal()">
                Kullanıcı Ekle
            </button>
        </div>
    </div>
    <div class="table-responsive" id="membersTable"></div>

@endcomponent

@component('modal-component',[
    "id" => "addUserToGroupModal",
    "title" => "Lütfen eklenecek kullanıcının adını girin.",
    
]) 

    <form onsubmit="return false;">
        <div class="form-group">
            <label for="addUserToGroupUser">{{__('Kullanıcı Adı')}}</label>
            <input class="form-control" id="addUserToGroupUser" aria-describedby="addUserToGroupUser" placeholder="{{__('Kullanıcı Adı')}}"/>
        </div>

        <input id="addUserToGroupGroup" type="hidden"/>

        <button class="btn btn-success" onclick="addUserToGroup()" style="float:right;">{{__('Kullanıcı Ekle')}}</button>
    </form>

@endcomponent

@component('modal-component',[
    "id" => "packageInstallerModal",
    "title" => "Görev İşleniyor",
])
@endcomponent


@component('modal-component',[
        "id" => "siteMigrate",
        "title" => "Migration",
    ])

    <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
      <li class="nav-item">
        <a class="nav-link active" id="ldapLoginTab" href="#ldapLogin" data-toggle="tab">{{__("LDAP'a Bağlan")}}</a>
      </li>

      <li class="nav-item">
        <a id="chooseSiteTab" class="nav-link" href="#chooseSite" data-toggle="tab" style="pointer-events: none;opacity: 0.4;">{{__('Site Seçimi')}}</a>
      </li>

    </ul>

    <div class="tab-content">
    <div id="ldapLogin" class="tab-pane active">
      <form>
        <div class="form-group">
          <label for="migrateIpAdress">{{__('IP adresi')}}</label>
          <input class="form-control" id="migrateIpAdress" aria-describedby="migrateIpAdressHelp" placeholder="{{__('IP adresi')}}">
          <small id="migrateIpAdressHelp" class="form-text text-muted">{{__('Göç edeceğiniz sunucunun IP adresini giriniz (192.168.1.10)')}}.</small>
        </div>
        <div class="form-group">
          <label for="migrateUsername">{{__('Kullanıcı adı')}}</label>
          <input class="form-control" id="migrateUsername" aria-describedby="migrateUsernameHelp" placeholder="{{__('Kullanıcı adı')}}">
          <small id="migrateUsernameHelp" class="form-text text-muted">{{__('Göç edeceğiniz sunucunun kullanıcı adını giriniz.')}}</small>
        </div>
        <div class="form-group">
          <label for="migratePassword">{{__('Parola')}}</label>
          <input type="password" class="form-control" id="migratePassword" placeholder="{{__('Parola')}}">
          <small id="migrateIpAdressHelp" class="form-text text-muted">{{__('Göç edeceğiniz sunucunun kullanıcı parolasını giriniz.')}}</small>
        </div>
      </form>
    <button class="btn btn-primary" onclick="ldapLogin()" style="float:right;">{{__('Bağlantıyı Kontrol Et ')}}<i class="fas fa-plug"></i></button>

    </div>

    <div id="chooseSite" class="tab-pane bd-example">
      <div class="alert alert-primary d-flex align-items-center " role="alert">
        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
          <i class="fas fa-icon mr-2"></i>
          <div>
            {{__('Site seçiminizi aşağıdaki listeden yapabilirsiniz.')}} 
          </div>
      </div>
      <br />
      @include('inputs', [
          "inputs" => [
              "Site Listesi:select_site" => [],
          ]
      ])
      <br />
      <br />
      <button class="btn btn-success" onclick="startSiteMigration()" style="float:right;">{{__('Başlat')}} </button>
    </div>
    
</div>
    
@endcomponent

@component('modal-component',[
    "id" => "replModal",
    "title" => "İstediğiniz replikasyonu seçiniz.",
    "footer" => [
        "text" => "Replike Et",
        "class" => "btn-success",
        "onclick" => "replicate()"
    ]
])

@include('inputs', [
    "inputs" => [
        "Nereden:fromSrv" => [
        ],

        "Nereye:toSrv" => [
        ],

        "Replikasyon Türü:replType" => [
            "Root" => "Root",
            "ForestDnsZones" => "ForestDnsZones",
            "Configuration" => "Configuration",
            "DomainDnsZones" => "DomainDnsZones",
            "Schema" => "Schema"
        ],
    ]
])
<div class="form-check">
    <input class="form-check-input" type="checkbox" value="" id="FullSync">
    <label class="form-check-label" for="FullSync">
        Full Sync
    </label>
</div>

@endcomponent

@component('modal-component',[
            "id" => "createSiteModal",
            "title" => "Lütfen oluşturmak istediğiniz site ismini yazınız",
            
        ]) 
    <form>
        <div class="form-group">
            <label for="sitename">{{__('Site adı')}}</label>
            <input class="form-control" id="sitename" aria-describedby="sitenameHelp" placeholder="{{__('Site adı')}}">
            <small id="sitenameHelp" class="form-text text-muted">{{__('Oluşturacağınız site adını giriniz.')}}</small>
        </div>
    </form>
        <button class="btn btn-success" onclick="createSite()" style="float:right;">{{__('Oluştur')}}</button>
@endcomponent

@component('modal-component',[
    "id" => "viewServersOfSiteModal"
])
<div id="serversOfSite-table" class="table-content">
    <div class="table-body"> </div>
</div>
@endcomponent

@component('modal-component',[
    "id" => "viewAvailableServersOfSiteModal"
])
<div id="availableServers-table" class="table-content">
    <div class="table-body"> </div>
</div>
@endcomponent

@component('modal-component',[
    "id" => "createUserModal",
    "title" => "Lütfen oluşturmak istediğiniz kullanıcının bilgilerini giriniz",
    
]) 
<form>
    <div class="form-group">
        <label for="usernameCreate">{{__('Kullanıcı adı')}}</label>
        <input class="form-control" id="usernameCreate" aria-describedby="usernameHelp" placeholder="{{__('Kullanıcı adı')}}">
        <small id="usernameHelp" class="form-text text-muted">{{__('Oluşturacağınız kullanıcının adını giriniz.')}}</small>
    </div>
    <div class="form-group">
        <label for="passwordCreate">{{__('Parola')}}</label>
        <input type="password" class="form-control" id="passwordCreate" placeholder="{{__('Parola')}}">
        <small id="passwordHelp" class="form-text text-muted">{{__('Oluşturacağınız kullanıcının parolasını giriniz.')}}</small>
    </div>
</form>
<button class="btn btn-success" onclick="createUser()" style="float:right;">{{__('Oluştur')}}</button>
@endcomponent
@component("modal-component", [
    "id" => "attributesModal",
    "title" => "Kullanıcı Bilgileri",
    "footer" => 
        [
        "class" => "btn-success",
        "onclick" => "closeAttrModal()",
        "text" => "Kapat"
        ]
    ])    
    <div class="clickedname"></div>

@endcomponent


@component('modal-component',[
    "id" => "updateAttributeModal",
    "title" => "Lütfen güncellenecek bilgileri girip kaydedin.",
    
]) 

<form onsubmit="return false;">
    <div class="form-group">
        <label for="updateAttributeKey">{{__('Nitelik')}}</label>
        <input class="form-control" id="updateAttributeKey" aria-describedby="updateAttributeKey" placeholder="{{__('Nitelik')}}" disabled/>
    </div>
    <div class="form-group">
        <label for="updateAttributeValue">{{__('Değer')}}</label>
        <input type="text" class="form-control" id="updateAttributeValue" placeholder="{{__('Değer')}}">
        <small id="updateAttributeValueHelp" class="form-text text-muted">{{__('Yeni değeri giriniz.')}}</small>
    </div>

    <input id="updateAttributeSamaccountname" type="hidden"/>

    <button class="btn btn-success" onclick="updateAttribute()" style="float:right;">{{__('Güncelle')}}</button>
</form>

@endcomponent