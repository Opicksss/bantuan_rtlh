<div class="vertical-menu">

    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="" class="logo logo-dark">
            <span class="logo-sm">
                <img src="img/logo.png" alt="logo-sm-dark" height="50">
            </span>
            <span class="logo-lg">
                <img src="img/logo.png" alt="logo-dark" height="50">
            </span>
        </a>

        <a href="index.html" class="logo logo-light">
            <span class="logo-sm">
                <img src="img/logo.png" alt="logo-sm-light" height="24">
            </span>
            <span class="logo-lg">
                <img src="img/logo.png" alt="logo-light" height="22">
            </span>
        </a>
    </div>

    <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect vertical-menu-btn"
        id="vertical-menu-btn">
        <i class="ri-menu-2-line align-middle"></i>
    </button>

    <div data-simplebar class="vertical-scroll">

        <!--- Sidemenu -->
        <div id="sidebar-menu">


            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Menu</li>

                <li>
                    <a href="{{ route('dashboard') }}"
                        class="waves-effect {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="uim uim-airplay"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('kriteria.index') }}"
                        class="waves-effect {{ request()->routeIs('kriteria.*', 'subKriteria.*') ? 'active' : '' }}">
                        <i class="uim uim-layers-alt"></i>
                        <span>Kriteria</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('alternatif.index') }}"
                        class="waves-effect {{ request()->is('alternatif*') ? 'active' : '' }}">
                        <i class="uim uim-layer-group"></i>
                        <span>Warga</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('hasil-saw.index') }}"
                        class="waves-effect {{ request()->is('hasil-saw*') ? 'active' : '' }}">
                        <i class="uim uim-horizontal-align-left"></i>
                        <span>Perhitungan SAW</span>
                    </a>
                </li>

                <li>
                    <a href="" class="waves-effect {{ request()->is('topsis*') ? 'active' : '' }}">
                        <i class="uim uim-graph-bar"></i>
                        <span>Perankingan</span>
                    </a>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="uim uim-lock-access"></i>
                        <span>Management Akun</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('management-akun.admin') }}">Admin</a></li>
                        <li><a href="{{ route('management-akun.verifikator') }}">Verifikator</a></li>
                    </ul>
                </li>
            </ul>

        </div>
        <!-- Sidebar -->
    </div>



</div>
