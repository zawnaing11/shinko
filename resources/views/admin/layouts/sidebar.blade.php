<div class="sidebar">
    <!-- Start Logobar -->
    <div class="logobar">
        <a href="{{ route('admin.notifications.index') }}" class="logo logo-large"><img src="{{ asset('assets/admin/images/logo.svg') }}" class="img-fluid" alt="logo"></a>
        <a href="{{ route('admin.notifications.index') }}" class="logo logo-small"><img src="{{ asset('assets/admin/images/small_logo.svg') }}" class="img-fluid" alt="logo"></a>
    </div>
    <!-- End Logobar -->
    <!-- Start Navigationbar -->
    <div class="navigationbar">
        <ul class="vertical-menu">
            <li id="notifications">
                <a href="{{ route('admin.notifications.index') }}">
                    <i class="fa fa-bullhorn"></i><span>お知らせ管理</span>
                </a>
            </li>
            <li id="stores">
                <a href="{{ route('admin.stores.index') }}">
                    <i class="ri-store-fill"></i><span>店舗管理</span>
                </a>
            </li>
        </ul>
    </div>
    <!-- End Navigationbar -->
</div>
