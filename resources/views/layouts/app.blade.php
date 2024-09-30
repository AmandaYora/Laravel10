
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Responsive HTML Admin Dashboard Template based on Bootstrap 5">
    <meta name="author" content="NobleUI">
    <meta name="keywords"
        content="nobleui, bootstrap, bootstrap 5, bootstrap5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

    <title>@yield('title', 'NobleUI Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!-- End fonts -->

    <!-- core:css -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/core/core.css') }}">
    <!-- endinject -->

    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <!-- End plugin css for this page -->

    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather-font/css/iconfont.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/flag-icon-css/css/flag-icon.min.css') }}">
    <!-- endinject -->

    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <!-- End plugin css for this page -->

    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/demo1/style.css') }}">
    <!-- End layout styles -->

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css">

    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.0/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .truncate {
            display: inline-block;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            vertical-align: middle;
        }
    </style>
    <style>
        .role-selector {
            position: fixed;
            top: 80px;
            right: 10px;
            z-index: 1050;
        }
    
        .btn-role-selector {
            background-color: #4a5568;
            border: none;
            border-radius: 50%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            opacity: 0.3;
            transition: all 0.3s ease;
        }
    
        .btn-role-selector i {
            font-size: 18px;
        }
    
        .btn-role-selector:hover {
            background-color: #2d3748;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            opacity: 1;
        }
    
        .btn-role-selector:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(66, 153, 225, 0.5);
        }
    
        .modal-dialog-custom {
            position: fixed;
            top: 140px;
            right: 10px;
            margin: 0;
            max-width: 240px;
        }
    
        .modal-content {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    
        .modal-body {
            padding: 8px;
        }
    
        .form-check {
            margin-bottom: 8px;
        }
    
        .form-check-label {
            cursor: pointer;
            font-size: 14px;
        }
    
        .modal-body p {
            text-align: center;
            color: #6c757d;
            margin: 0;
            font-size: 14px;
        }
    </style>
    @yield('styles')
</head>

<body>
    <div class="main-wrapper">
        @include('partials.sidebar') <!-- Sidebar -->
        <div class="page-wrapper">
            @include('partials.navbar') <!-- Navbar -->
            <div class="page-content">
                @yield('content') <!-- Content -->
            </div>
            @include('partials.footer') <!-- Footer -->
        </div>
    </div>

    <!-- Role Selector Button -->
    <div class="role-selector">
        <button type="button" class="btn-role-selector" data-bs-toggle="modal" data-bs-target="#roleModal">
            <i class="fas fa-user-cog"></i>
        </button>
    </div>

    <!-- Modal yang muncul di bawah tombol -->
    <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-custom">
            <div class="modal-content">
                <div class="modal-body">
                    <form method="POST" action="{{ route('change.role') }}">
                        @csrf
                        @if(isset($currentUser->data->roles) && is_array($currentUser->data->roles))
                            @foreach($currentUser->data->roles as $role)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" 
                                           name="activeRole" 
                                           id="role{{ $role['role_id'] }}" 
                                           value="{{ $role['role_id'] }}"
                                           @if($activeRole['role_id'] == $role['role_id']) checked @endif
                                           onchange="this.form.submit()">
                                    <label class="form-check-label" for="role{{ $role['role_id'] }}">
                                        {{ $role['role_name'] }}
                                    </label>
                                </div>
                            @endforeach
                        @else
                            <p>Tidak ada role tersedia.</p>
                        @endif
                    </form> 
                </div>
            </div>
        </div>
    </div>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- core:js -->
    <script src="{{ asset('assets/vendors/core/core.js') }}"></script>
    <!-- endinject -->

    <!-- Plugin js for this page -->
    <script src="{{ asset('assets/vendors/chartjs/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery.flot/jquery.flot.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery.flot/jquery.flot.resize.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
    <!-- End plugin js for this page -->

    <!-- inject:js -->
    <script src="{{ asset('assets/vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/template.js') }}"></script>
    <!-- endinject -->

    <!-- Custom js for this page -->
    <script src="{{ asset('assets/js/dashboard-light.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker.js') }}"></script>
    <!-- End custom js for this page -->

    <!-- Plugin js for this page -->
    <script src="{{ asset('assets/vendors/datatables.net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
    <!-- End plugin js for this page -->

    <!-- Custom js for this page -->
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <!-- End custom js for this page -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js"
        integrity="sha512-u3fPA7V8qQmhBPNT5quvaXVa1mnnLSXUep5PS1qo5NRzHwG19aHmNJnj1Q8hpA/nBWZtZD4r4AX6YOt5ynLN2g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.0/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrious/dist/qrious.min.js"></script>
    @include('layouts.snackbar')
    <script src="https://unpkg.com/feather-icons"></script>
    @yield('scripts')

    <script>
        feather.replace();
    </script>

</body>

</html>
