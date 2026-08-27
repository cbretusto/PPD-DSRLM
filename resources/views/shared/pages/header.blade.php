<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="" class="nav-link">PPD Die-set Renewal and Longevity Monitoring</a>
            </li>
        </ul>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i>
                    @php
                    if(isset($_SESSION['rapidx_user_id'])){
                        echo $_SESSION['rapidx_name'];
                    }
                    @endphp
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            </li>
        </ul>
    </div>
</nav>
