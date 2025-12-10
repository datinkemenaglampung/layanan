<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Admin</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset ('assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('assets/modules/fontawesome/css/all.min.css') }}">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset ('assets/modules/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css') }}">

    <link rel="stylesheet" href="{{ asset ('assets/modules/select2/dist/css/select2.min.css') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset ('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset ('assets/css/components.css') }}">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset ('assets/modules/izitoast/css/iziToast.min.css') }}">


    <!-- NesTable -->
    <link rel="stylesheet" href="{{ asset ('assets/modules/nestable/nestable.css') }}">
    <!-- Start GA -->
    @yield('css')
</head>

<body>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar">
                <form class="form-inline mr-auto">
                    <ul class="navbar-nav mr-3">
                        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
                    </ul>
                </form>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                            <img alt="image" src="{{ asset('storage/user/' . Auth::user()->image) }}" class="rounded-circle me-1">
                            <div class="d-sm-none d-lg-inline-block">Hi, {{ Auth::user()->name }}</div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="{{route('users.profile')}}" class="dropdown-item">
                                    <i class="far fa-user"></i> Profile
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a href="{{ route('logout') }}" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>

            </nav>
            <div class="main-sidebar sidebar-style-2">
                <aside id="sidebar-wrapper">
                    <div class="sidebar-brand">
                        <a href="">Admin</a>
                    </div>
                    <div class="sidebar-brand sidebar-brand-sm">
                        <a href="">A</a>
                    </div>
                    <ul class="sidebar-menu">
                        <li class="menu-header">Starter</li>
                        {!! App\Classes\Theme\Menu::sidebar() !!}
                    </ul>
                </aside>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1>{{$config['title']}}</h1>
                        <div class="section-header-breadcrumb">
                            <div class="breadcrumb-item active"><a href="#">{{$config['title']}}</a></div>
                            <div class="breadcrumb-item">List</div>
                        </div>
                    </div>
                    <div class="section-body">
                        @yield('content')
                    </div>
                </section>
            </div>
            <footer class="main-footer">
                <div class="footer-left">
                    Copyright &copy; 2018 <div class="bullet"></div> Develope By <a href="">Datin</a>
                </div>
                <div class="footer-right">

                </div>
            </footer>
        </div>
    </div>

    <!-- General JS Scripts -->
    <script src="{{ asset ('assets/modules/jquery.min.js') }}"></script>
    <script src="{{ asset ('assets/modules/popper.js') }}"></script>
    <script src="{{ asset ('assets/modules/tooltip.js') }}"></script>
    <script src="{{ asset ('assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset ('assets/modules/moment.min.js') }}"></script>
    <script src="{{ asset ('assets/js/stisla.js') }}"></script>

    <!-- JS Libraies -->
    <script src="{{ asset ('assets/modules/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset ('assets/modules/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset ('assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset ('assets/modules/jquery-ui/jquery-ui.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset ('assets/js/page/modules-datatables.js') }}"></script>
    <script src="{{ asset ('assets/modules/select2/dist/js/select2.full.min.js') }}"></script>

    <!-- JS Libraies -->
    <script src="{{ asset ('assets/modules/sweetalert/sweetalert.min.js') }}"></script>

    <!-- JS Libraies -->
    <script src="{{ asset ('assets/modules/izitoast/js/iziToast.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset ('assets/js/page/modules-toastr.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset ('assets/js/page/modules-sweetalert.js') }}"></script>

    <script src="{{ asset ('assets/js/scripts.js') }}"></script>
    <script src="{{ asset ('assets/js/custom.js') }}"></script>

    <!-- Nestable -->
    <script src="{{ asset ('assets/modules/nestable/nestable.js') }}"></script>
    @yield('modal')
    @yield('script')
</body>

</html>