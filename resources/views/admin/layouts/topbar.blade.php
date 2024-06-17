<div class="topbar">
    <!-- Start row -->
    <div class="row align-items-center">
        <!-- Start col -->
        <div class="col-md-12 align-self-center">
            <div class="togglebar">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item">
                        <div class="menubar">
                            <a class="menu-hamburger" href="javascript:void();">
                                <i class="ri-menu-2-line menu-hamburger-collapse"></i>
                                <i class="ri-close-line menu-hamburger-close"></i>
                             </a>
                         </div>
                    </li>
                </ul>
            </div>
            <div class="infobar">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item">
                        <div class="profilebar">
                            <div class="dropdown">
                              <a class="dropdown-toggle" href="#" role="button" id="profilelink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="{{ asset('assets/admin/images/users/profile.svg') }}" class="img-fluid" alt="profile"><span class="live-icon">{{ Auth::user()->name }}</span></a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profilelink">
                                    <form method="POST" name="logout" action="{{ route('admin.logout') }}">
                                        @csrf
                                        <a class="dropdown-item text-danger" href="javascript:logout.submit()">
                                            <i class="ri-shut-down-line"></i>ログアウト
                                        </a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <!-- End col -->
    </div>
    <!-- End row -->
</div>
<!-- Start Breadcrumbbar -->
<div class="breadcrumbbar">
    <div class="row align-items-center">
        <div class="col-md-8 col-lg-8">
            <h4 class="page-title">@yield('title')</h4>
            @yield('breadcrumb')
        </div>
        <div class="col-md-4 col-lg-4">
            <div class="widgetbar">
                @yield('widgetbar')
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumbbar -->
