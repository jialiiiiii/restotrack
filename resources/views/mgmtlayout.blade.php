<head>
    <meta charset="UTF-8">
    <title>Manage | Perfecto Pizzas</title>
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
        :root {
            --header-height: 3rem;
            --nav-width: 70px;
            --first-color: #ac1820;
            --first-color-light: #f9f4df;
            --body-font: Georgia, 'Times New Roman', serif;
            --normal-font-size: 1rem;
            --z-fixed: 100;
        }

        *,
        ::before,
        ::after {
            box-sizing: border-box;
        }

        body {
            position: relative;
            margin: var(--header-height) 0 0 0;
            padding: 0 1rem;
            font-family: var(--body-font);
            font-size: var(--normal-font-size);
            transition: .5s;
        }

        a {
            text-decoration: none;
        }

        .header {
            width: 100%;
            height: var(--header-height);
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            z-index: var(--z-fixed);
            transition: .5s;
            background: #f4e1cd;
        }

        .header_toggle {
            color: var(--first-color);
            font-size: 1.5rem;
            cursor: pointer;
        }

        .header_text {
            font-weight: bold;
            display: flex;
            flex-direction: column;
            align-items: end;
        }

        .header_text #current-datetime {
            display: flex;
            align-items: center;
            letter-spacing: 0.4px;
        }

        .l-navbar {
            position: fixed;
            top: 0;
            left: -30%;
            width: var(--nav-width);
            height: 100vh;
            background-color: var(--first-color);
            padding: .5rem 1rem 0 0;
            transition: .5s;
            z-index: var(--z-fixed);
        }

        .nav {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .nav_logo,
        .nav_link {
            display: grid;
            grid-template-columns: max-content max-content;
            align-items: center;
        }

        .nav_link {
            padding: .5rem 0 .5rem 1.5rem;
            column-gap: 1rem;
        }

        .nav_logo {
            padding: .5rem 0 .5rem .4rem;
            column-gap: .5rem;
            margin-bottom: 1rem;
        }

        .nav_logo-name {
            font-family: Georgia, 'Times New Roman', serif;
            font-weight: 700;
            color: var(--first-color-light);
        }

        .nav_link {
            position: relative;
            color: var(--first-color-light);
            margin-bottom: 1.5rem;
            transition: .3s;
        }

        .nav_link:hover {
            color: #ffca2b;
        }

        .nav_icon {
            font-size: 1.2rem;
            width: 1.5rem;
        }

        #nav-bar.show {
            left: 0;
        }

        .body-pd {
            padding-left: calc(var(--nav-width) + 1rem);
        }

        .active {
            color: #ffca2b;
        }

        .active::before {
            content: '';
            position: absolute;
            left: 0;
            width: 2px;
            height: 32px;
            background-color: #ffca2b;
        }

        @media screen and (min-width: 768px) {
            body {
                margin: calc(var(--header-height) + 1rem) 0 0 0;
                padding-left: calc(var(--nav-width) + 2rem);
                padding-right: 2rem;
            }

            .header {
                height: calc(var(--header-height) + 1rem);
                padding: 0 2rem 0 calc(var(--nav-width) + 2rem);
            }

            .l-navbar {
                left: 0;
                padding: .2rem .7rem 0 0;
            }

            #nav-bar.show {
                width: calc(var(--nav-width) + 138px);
            }

            .body-pd {
                padding-left: calc(var(--nav-width) + 168px);
            }
        }
    </style>

    @yield('head')
</head>

<body id="body-pd">
    <header class="header" id="header">
        <div class="header_toggle"> <i class="fas fa-bars" id="header-toggle"></i> </div>
        <div class="header_text">
            <div>Hi, {{ auth()->guard('staff')->user()->name ?? 'Guest' }}</div>
            <div id="current-datetime"></div>
        </div>
    </header>
    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div>
                <a href="/home" class="nav_logo pl-3">
                    <img src="/img/logo.png" width="55" /><span class="nav_logo-name">Perfecto Pizzas</span>
                </a>
                <div class="nav_list">
                    <a href="/tables" class="nav_link {{ Request::is('table*') ? 'active' : '' }}">
                        <i class="fas fa-chair nav_icon"></i> <span class="nav_name">Tables</span>
                    </a>
                    <a href="/orders" class="nav_link {{ Request::is('order*') ? 'active' : '' }}">
                        <i class="fas fa-list-ul nav_icon"></i> <span class="nav_name">Orders</span>
                    </a>
                    <a href="/reservations" class="nav_link {{ Request::is('reservation*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check nav_icon"></i> <span class="nav_name">Reservations</span>
                    </a>
                    <a href="/meals" class="nav_link {{ Request::is('meal*') ? 'active' : '' }}">
                        <i class="fas fa-utensils nav_icon"></i> <span class="nav_name">Meals</span>
                    </a>
                    @if (auth()->guard('staff')->user()->hasRole('admin'))
                        <a href="/customers" class="nav_link {{ Request::is('customer*') ? 'active' : '' }}">
                            <i class="fas fa-user nav_icon"></i></i> <span class="nav_name">Customers</span>
                        </a>
                        <a href="/staff" class="nav_link {{ Request::is('staff*') ? 'active' : '' }}">
                            <i class="fas fa-user-cog nav_icon"></i> <span class="nav_name">Staff</span>
                        </a>
                    @endif
                </div>
            </div>
            <a href="/logout" class="nav_link">
                <i class="fas fa-sign-out-alt nav_icon"></i> <span class="nav_name">Sign Out</span>
            </a>
        </nav>
    </div>

    <div id="l-content">
        <h4 class="fw-bold title-blue py-3">@yield('title')</h4>

        @yield('body')
    </div>
</body>

<script>
    @if (session()->has('msg') && session()->get('msg') == 'loginSuccess')
        Swal.fire({
            icon: 'success',
            title: 'Welcome',
            text: 'You have logged in successfully!',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6',
        })
    @endif

    function updateTime() {
        var dateTime = new Date();
        var day = dateTime.getDate();
        var month = dateTime.getMonth() + 1;
        var year = dateTime.getFullYear();
        var hour = dateTime.getHours();
        var minutes = dateTime.getMinutes();

        if (day < 10) day = '0' + day;
        if (month < 10) month = '0' + month;
        if (hour < 10) hour = '0' + hour;
        if (minutes < 10) minutes = '0' + minutes;

        var formattedDateTime = day + '-' + month + '-' + year + ' ' + hour + ':' + minutes;
        var dateTimeElement = document.getElementById("current-datetime");
        dateTimeElement.innerHTML = formattedDateTime;
    }
    updateTime();
    setInterval(updateTime, 1000);

    document.addEventListener("DOMContentLoaded", function(event) {

        const showNavbar = (toggleId, navId, bodyId, headerId) => {
            const toggle = document.getElementById(toggleId),
                nav = document.getElementById(navId),
                bodypd = document.getElementById(bodyId),
                headerpd = document.getElementById(headerId)

            // validate that all variables exist
            if (toggle && nav && bodypd && headerpd) {
                toggle.addEventListener('click', () => {
                    // show navbar
                    nav.classList.toggle('show')
                    // change icon
                    toggle.classList.toggle('fa-times')
                    // add padding to body
                    bodypd.classList.toggle('body-pd')
                    // add padding to header
                    headerpd.classList.toggle('body-pd')
                })
            }
        }

        showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header')

        const linkColor = document.querySelectorAll('.nav_link')

        function colorLink() {
            if (linkColor) {
                linkColor.forEach(l => l.classList.remove('active'))
                this.classList.add('active')
            }
        }
        linkColor.forEach(l => l.addEventListener('click', colorLink))
    });
</script>
