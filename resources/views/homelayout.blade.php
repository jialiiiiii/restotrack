<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>
        @hasSection('title')
            @yield('title') | Perfecto Pizzas
        @else
            Perfecto Pizzas
        @endif
    </title>
    <link rel="icon" type="image/x-icon" href="/img/logo.ico">
    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Fafa icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Sweetalert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom -->
    <link href="/css/base.css" rel="stylesheet">
    <script src="/js/base.js"></script>

    <style>
        /* Header */
        .navbar {
            background-color: #ac1820;
        }

        .navbar-brand {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 1.4rem;
            font-weight: bold;
        }

        @media only screen and (max-width: 360px) {
            .navbar-brand {
                font-size: 1.2rem;
                width: 25%;
            }

            .navbar-brand img {
                width: 90%;
            }
        }

        .nav-link {
            color: #f9f4df !important;
            padding: 0;
        }

        .nav-link:hover,
        .nav-link:focus,
        .nav-link.active {
            color: #ffca2b !important;
            transition: color .2s linear;
        }

        @media only screen and (max-width: 991px) {
            .nav-item {
                margin-left: 15px;
                line-height: 2;
            }

            .nav-link.border-end {
                border: none !important;
            }
        }

        .dropdown-menu {
            min-width: max-content;
            background-color: #ac1820;
        }

        .dropdown-item {
            color: #f9f4df !important;
        }

        .dropdown-item:focus,
        .dropdown-item:hover {
            background-color: transparent;
            color: #ffca2b !important;
            transition: color .2s linear;
        }

        .dropdown-toggle::after {
            vertical-align: middle;
        }

        /* Footer */
        footer {
            background-color: #ac1820;
            color: #f9f4df;
            padding: 30px 0;
        }

        footer .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        footer .social-links {
            text-align: center;
            padding-bottom: 20px;
        }

        .footer-icon {
            color: #f9f4df;
            font-size: 20pt;
            text-decoration: none;
            margin-left: 10px;
            margin-right: 10px;
        }

        .footer-icon:hover {
            color: #ffca2b;
            transition: color .2s linear;
        }

        @media only screen and (max-width: 425px) {
            footer {
                font-size: .9rem;
            }

            footer .copyright img {
                display: none;
            }

            footer .social-links {
                padding-bottom: 10px;
            }

            .footer-icon {
                font-size: 18pt;
            }
        }

        .truncate {
            display: inline-block;
            max-width: 150px;
            vertical-align: top;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .underline {
            border-bottom: 2px solid #f9f4df;
        }

        .nav-link:hover .underline,
        .nav-link:focus .underline,
        .nav-link.active .underline {
            border-bottom-color: #ffca2b !important;
            transition: border-bottom-color .2s linear;
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.15rem;
        }
    </style>

    @yield('head')
</head>

<body>
    @php
        $page = request()->path();
    @endphp

    <nav class="navbar fixed-top navbar-expand-lg navbar-dark p-0">
        <div class="container-fluid">
            <a class="navbar-brand" href="/home">
                <img src="/img/logo.png" width="80" class="d-inline-block align-middle">
                <span class="align-middle">Perfecto Pizzas</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll"
                aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse navbar-nav-scroll" id="navbarScroll">
                <ul class="navbar-nav ms-auto me-3 my-2 my-lg-0" style="--bs-scroll-height: 100px;">
                    <li class="nav-item">
                        <a class="nav-link {{ $page === 'home' ? 'active' : '' }}" aria-current="page"
                            href="/home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $page === 'menu' ? 'active' : '' }}" href="/menu">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $page === 'reservations/create' ? 'active' : '' }}" href="/reservations/create">Reserve</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $page === 'track' ? 'active' : '' }}" href="/track">Track</a>
                    </li>
                </ul>
                <ul class="navbar-nav me-3 my-2 my-lg-0" style="--bs-scroll-height: 100px;">
                    @if (auth()->guard('customer')->check())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="underline">Hi,
                                    {{ auth()->guard('customer')->user()->name ?? 'Guest' }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                                <a class="dropdown-item" href="/orders/view">Orders</a>
                                <a class="dropdown-item" href="/reservations/view">Reservations</a>
                                <a class="dropdown-item" href="/customers/profile">Profile</a>
                                <a class="dropdown-item" href="/logout">Logout</a>
                            </div>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link border-end <?= $page == 'customers/register' ? 'active' : '' ?>"
                                href="/customers/register">Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $page == 'login' ? 'active' : '' ?>" href="/login">Login</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    @yield('body')

    <footer>
        <div class="container social-links">
            <a class="footer-icon" href="https://www.facebook.com">
                <i class="fab fa-facebook"></i>
            </a>
            <a class="footer-icon" href="https://www.instagram.com">
                <i class="fab fa-instagram"></i>
            </a>
            <a class="footer-icon" href="https://www.twitter.com">
                <i class="fab fa-twitter"></i>
            </a>
            <a class="footer-icon" href="https://www.youtube.com">
                <i class="fab fa-youtube"></i>
            </a>
            <a class="footer-icon" href="https://www.linkedin.com">
                <i class="fab fa-linkedin-in"></i>
            </a>
            <a class="footer-icon" href="https://www.pinterest.com">
                <i class="fab fa-pinterest"></i>
            </a>
        </div>

        <div class="container copyright">
            <a href="/home" class="me-2 lh-1 text-decoration-none">
                <img src="/img/logo.png" width="60">
            </a>
            <span>&copy; 2023 Perfecto Pizzas. All rights reserved.</span>
        </div>
    </footer>

    @if (session()->has('msg'))
        <script>
            @if (session()->get('msg') == 'loginSuccess')
                Swal.fire({
                    icon: 'success',
                    title: 'Welcome',
                    text: 'You have logged in successfully!',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                }).then((result) => {
                    @if (session()->has('to'))
                        window.location.href = '/{{ session()->pull('to') }}';
                    @endif
                })
            @elseif (session()->get('msg') == 'loginFail')
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Error encountered while logging in.',
                    showCancelButton: true,
                    cancelButtonText: 'Close',
                    confirmButtonText: 'Try Again',
                    confirmButtonColor: '#3085d6',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/login';
                    }
                })
            @elseif (session()->get('msg') == 'logoutSuccess')
                Swal.fire({
                    icon: 'success',
                    title: 'See You Again',
                    text: 'You have logged out successfully!',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                })
            @endif
        </script>
    @endif
</body>
