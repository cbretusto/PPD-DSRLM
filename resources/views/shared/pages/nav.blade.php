
@php
    if (isset($_SESSION['ppd_dsrlm_user_level'])) {
        $userLevel = $_SESSION['ppd_dsrlm_user_level'];
    } else {
        $userLevel = 0;
    }
@endphp

<aside class="app-sidebar sidebar-expand-lg sidebar-collaps bg-body-secondary shadow"
    data-bs-theme="dark">

    <div class="sidebar-brand">
        <a href="" class="brand-link text-center">
            <img src=""
                class="brand-image img-circle elevation-3"
                style="opacity: .8">

            <span class="brand-text font-weight-light font-size">
                <h5>PPD - DSRLM</h5>
            </span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview"
                role="menu"
                data-accordion="false">

                <li class="nav-item">
                    <a href="{{ url('../RapidX') }}" class="nav-link">
                        <i class="nav-icon fa-solid fa-right-to-bracket fa-flip-horizontal fa-lg"></i>
                        <span>Return to RapidX</span>
                    </a>
                </li>
                <br>
                <li class="nav-header">
                    <strong>SYSTEM NAME</strong>
                </li>

                @if($userLevel == 2)
                    <li class="nav-item">
                        <a href="{{ route('user_management') }}" class="nav-link">
                            <i class="nav-icon fa-solid fa-users-gear"></i>
                            <span>User Management</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('remark') }}" class="nav-link">
                            <i class="nav-icon fa-solid fa-gears"></i>
                            <span>Setting</span>
                        </a>
                    </li>
                @endif

                <li class="nav-item">
                    <a href="{{ route('device') }}" class="nav-link">
                        <i class="nav-icon fas fa-microchip"></i>
                        <span>Devices</span>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
