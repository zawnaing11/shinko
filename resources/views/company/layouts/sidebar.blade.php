<div class="sidebar">
    <!-- Start Logobar -->
    <div class="logobar">
        <a href="{{ route('company.product_prices.index') }}" class="logo logo-large"><img src="{{ asset('assets/admin/images/logo.svg') }}" class="img-fluid" alt="logo"></a>
        <a href="{{ route('company.product_prices.index') }}" class="logo logo-small"><img src="{{ asset('assets/admin/images/small_logo.svg') }}" class="img-fluid" alt="logo"></a>
    </div>
    <!-- End Logobar -->
    <!-- Start Navigationbar -->
    <div class="navigationbar">
        <ul class="vertical-menu">
            <li id="product_prices">
                <a href="{{ route('company.product_prices.index') }}">
                    <i class="ri-money-cny-circle-line"></i><span>商品価格管理</span>
                </a>
            </li>
            <li id="users">
                <a href="{{ route('company.users.index') }}">
                    <i class="ri-user-fill"></i><span>ユーザー管理</span>
                </a>
            </li>
            <li id="imports">
                <a href="{{ route('company.imports.index') }}">
                    <i class="fa fa-list-ul"></i><span>CSVインポート管理</span>
                </a>
            </li>
        </ul>
    </div>
    <!-- End Navigationbar -->
</div>
