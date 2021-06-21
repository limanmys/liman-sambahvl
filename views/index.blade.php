<ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
    <li class="nav-item">
        <a class="nav-link active"  onclick="tab1()" href="#tab1" data-toggle="tab">
        <i class="fas fa-download mr-2"></i>
        Kurulum</a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link " onclick="tab2()" href="#tab2" data-toggle="tab">
        <i class="fas fa-rss-square mr-2"></i>
        Etki Alanı Oluştur</a>
    </li>

    <li class="nav-item">
        <a class="nav-link "  onclick="tab3()" href="#tab3" data-toggle="tab">
        <i class="fas fa-info mr-2"></i>
        Samba Servis Durumu</a>
    </li>

    <li class="nav-item">
        <a class="nav-link " onclick="migration()" href="#migration"  data-toggle="tab">
        <i class="fas fa-bezier-curve mr-2"></i>
        Migration</a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link "  onclick="printTable()" href="#fsmo" data-toggle="tab">
        <i class="fas fa-id-card mr-2"></i>
        FSMO Rol Yönetimi</a>
    </li>

    <li class="nav-item">
        <a class="nav-link "  onclick="trustedServers()" href="#trustRelation" data-toggle="tab">
        <i class="fas fa-shield-alt mr-2"></i>
        Trusted Servers</a>
    </li>

    <li class="nav-item">
        <a class="nav-link "  onclick="replicationInfo()" href="#replication" data-toggle="tab">
        <i class="fas fa-retweet mr-2"></i>
        Replication Info</a>
    </li>

    
</ul>


<div class="tab-content">
    <div id="tab1" class="tab-pane active">
        @include('pages.install')
    </div>

    <div id="tab2" class="tab-pane">  
        @include('pages.domain')
    </div>

    <div id="tab3" class="tab-pane">   
        @include('pages.service_status')
    </div>

    <div id="fsmo" class="tab-pane">
        @include('pages.fsmo')
    </div>

    <div id="trustRelation" class="tab-pane">
        @include('pages.trust')
    </div>
    
    <div id="replication" class="tab-pane">
        @include('pages.replication')
    </div>

    <div id="migration" class="tab-pane">
        @include('pages.migration')
    </div>

</div>

<script>

    if(location.hash === ""){
        tab1();
    }
    
</script>