@extends('homelayout')
@section('title', 'Menu')

@section('head')
    <style>
        /* Menu & search */
        .card {
            background: #fbeee0;
            border: transparent;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease-in-out;
        }

        .card:hover {
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .card-footer {
            border-top: transparent;
            background: transparent;
            padding: 1.5rem 1rem;
            text-align: center;
        }

        .card-title {
            margin-bottom: 0.8rem;
            font-size: 1.1rem;
        }

        .card-text {
            display: -webkit-box;
            -webkit-line-clamp: 5;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .search-icon {
            position: absolute;
            right: 0.5rem;
            top: 0.6rem;
            font-size: 1.1rem;
            color: #1f579c;
            z-index: 5;
            cursor: pointer;
        }

        .cross-icon {
            position: absolute;
            right: 2.5rem;
            top: 0.6rem;
            font-size: 1.2rem;
            color: #1f579c;
            z-index: 5;
            display: none;
            cursor: pointer;
        }

        @media only screen and (max-width: 575px) {
            .custom-pagination {
                display: flex;
                justify-content: center !important;
            }
        }

        /* Add to cart */
        .cart-btn {
            position: fixed;
            left: 15px;
            bottom: 15px;
            z-index: 2;
            background: #764a3d;
            width: 60px;
            height: 60px;
            border-radius: 50px;
            transition: all 0.4s;
            text-decoration: none;
            box-shadow: 0 3px 6px rgba(118, 74, 61, 0.16), 0 3px 6px rgba(118, 74, 61, 0.23);
        }

        .cart-btn:hover {
            background: #7a5145;
        }

        .cart-btn i {
            font-size: 25px;
            color: #f9f4df;
            line-height: 0;
        }

        .cart-text {
            position: fixed;
            left: 31px;
            bottom: 31px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f6c329;
            color: #fff;
            border-radius: 50%;
            font-weight: bold;
            width: 28px;
            height: 28px;
            z-index: 3;
            box-shadow: 0 3px 6px rgba(255, 171, 0, 0.16), 0 3px 6px rgba(255, 171, 0, 0.23);
            transform: scale(0);
        }

        .cart-text.move {
            left: 58px;
            bottom: 55px;
            transition: .1s linear;
            transform: scale(1);
        }

        .circle {
            content: "";
            display: inline;
            position: absolute;
            width: 38px;
            height: 38px;
            margin-left: 38px;
            margin-top: -19px;
            background: #2664b1;
            z-index: -1;
        }

        .circle.move {
            transform: rotate(-180deg);
            z-index: 1;
            transition: .2s linear;
        }

        .circle.size {
            border-radius: 50%;
            transition: .2s linear;
        }
    </style>
@endsection

@section('body')
    <div class="body">
        <div id="result" class="d-flex flex-column mx-3 align-items-center">
            <p class="h2 fw-bold title-blue text-center">Menu</p>
            <form class="w-100 my-4">
                <div class="row">
                    <div class="col-12 col-md-4">
                        <select class="form-select" id="categorySelect">
                            <option value="all">All</option>
                            @if (!empty($categories))
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-12 col-md-4 mt-3 mt-md-0">
                        <select class="form-select" id="sortSelect">
                            <option value="old-new">Old - New</option>
                            <option value="new-old">New - Old</option>
                            <option value="low-high">Low Price - High Price</option>
                            <option value="high-low">High Price - Low Price</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4 mt-3 mt-md-0">
                        <div class="input-group">
                            <input type="text" class="form-control search-bar" placeholder="Search" name="search-menu"
                                id="search-menu" maxlength="50" value="{{ $query }}" aria-describedby="basic-addon1">
                            <span id="search-icon" class="search-icon"><i class="fas fa-search"></i></span>
                            <span id="cross-icon" class="cross-icon"><i class="fas fa-times"></i></span>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-2 g-sm-3 g-md-4 w-100" id="mealList">
                @if (count($meals) > 0)
                    @foreach ($meals as $m)
                        <div class="col">
                            <div class="card h-100">
                                <img src="/img/meals/{{ $m->id }}.png" class="card-img-top" alt="Meal Image">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold">{{ $m->name }}</h5>
                                    <p class="card-text">{{ $m->description }}</p>
                                    <p class="card-text">
                                        @if ($m->sales > 0)
                                            <span
                                                class="text-decoration-line-through me-1">RM{{ number_format($m->price, 2) }}</span>
                                            RM {{ number_format(($m->price * (100 - $m->sales)) / 100, 2) }}
                                        @else
                                            RM {{ number_format($m->price, 2) }}
                                        @endif
                                    </p>
                                </div>
                                <div class="card-footer">
                                    @if ($addToCart)
                                        <span class="circle"></span>
                                        <button class="add-btn btn btn-primary" id="{{ $m->id }}">Add to
                                            Cart</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 mx-auto my-5 d-flex flex-column align-items-center">
                        <img class="img-empty w-25" src="/img/empty-search.png" alt="Empty Search" />
                        <div class="mt-4">
                            There is no matching results.
                        </div>
                    </div>
                @endif
            </div>

            @if (count($meals) > 0)
                <div class="custom-pagination w-100 my-4">
                    {{ $meals->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="fas fa-arrow-up"></i></a>

    @if ($addToCart)
        <div class="cart">
            <a href="/carts" class="cart-btn d-flex align-items-center justify-content-center"><i
                    class="fas fa-shopping-cart"></i></a>
            <div class="cart-text">0</div>
        </div>
    @endif

    <script>
        $(document).ready(function() {
            //--------------------------------------
            // Add to cart
            //--------------------------------------   
            @if ($addToCart)

                @if (session()->has('cartTable'))
                    // Get user and type
                    getCartInfo();
                @endif

                var count = {{ $cartQuantity }};
                if (count > 0) {
                    $(".cart-text").text(count);
                    $(".cart-text").addClass("move");
                }

                var top = $('.cart-btn').offset().top - $(window).scrollTop();
                var left = $('.cart-btn').offset().left;

                $(".add-btn").one('click', function(event) {
                    // Animation
                    var elem = $(this).closest('.card-footer').find('.circle');

                    elem.addClass("size");
                    setTimeout(function() {
                        elem.addClass("move");
                        var x = -(((elem.offset().left - left) / 2) - left) + left - 5;
                        var y = (top - (elem.offset().top - $(window).scrollTop())) / 2 + 20;
                        elem.css('transform-origin', `${x}px ${y}px`);
                    }, 200);
                    setTimeout(function() {
                        elem.removeClass("move");
                        elem.css('transform-origin', 'initial');
                        elem.removeClass("size");
                    }, 600);

                    // Post data
                    var id = $(this).attr('id');
                    $.ajax({
                        type: 'POST',
                        url: '/carts',
                        data: {
                            mealId: id,
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            // Handle response
                            if (response.message == 'success') {
                                // Trigger reload
                                filterMeals();
                            }
                        },
                        error: function(error) {
                            // Handle errors
                            console.error(error);
                        }
                    });
                });
            @endif

            //--------------------------------------
            // Get user id and order type
            //--------------------------------------  
            function getCartInfo() {
                var name = '';
                var id = "{{ session()->get('cartUserId') }}";
                var user = "{{ session()->get('cartUser') }}";
                var type = "{{ session()->get('cartType') }}";

                var progressSteps = (user === '' && type === '') ? ['1', '2'] : [];

                const Queue = Swal.mixin({
                    progressSteps: progressSteps,
                    allowOutsideClick: false,
                    showClass: {
                        backdrop: 'swal2-noanimation'
                    },
                    hideClass: {
                        backdrop: 'swal2-noanimation'
                    },
                });


                (async () => {
                    if (user == '' || id == '') {
                        @if (auth()->guard('customer')->check())
                            name = "{{ auth()->guard('customer')->user()->name }}";
                            id = "{{ auth()->guard('customer')->user()->id }}";
                        @else
                            name = 'Guest';
                            id = "{{ session()->getId() }}";
                        @endif

                        await Queue.fire({
                            currentProgressStep: 0,
                            title: 'Hello',
                            text: 'Please choose how you want to proceed',
                            showCancelButton: true,
                            cancelButtonText: 'Continue as Guest',
                            confirmButtonText: (name === 'Guest') ? 'Login Now' : 'Continue as ' +
                                name,
                            confirmButtonColor: '#3085d6',
                            reverseButtons: true,
                            preConfirm: () => {
                                return new Promise((resolve) => {
                                    if (name === 'Guest') {
                                        window.location.href = '/login?from=menu';
                                        resolve(false);
                                    } else {
                                        resolve();
                                    }
                                });
                            }
                        }).then((result) => {
                            // Set session for cartUser
                            $.ajax({
                                type: 'POST',
                                url: '/carts/session',
                                data: {
                                    user: name,
                                    userId: id,
                                    _token: '{{ csrf_token() }}',
                                }
                            });
                        })
                    }

                    if (type == '') {
                        await Queue.fire({
                            currentProgressStep: 1,
                            title: 'Hello',
                            text: 'Please select your order type',
                            showCancelButton: true,
                            cancelButtonText: 'Takeaway',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Dine-in',
                            confirmButtonColor: '#3085d6',
                            reverseButtons: true,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                type = 'dine-in';
                            } else if (result.isDismissed) {
                                type = 'takeaway';
                            }

                            // Set session for cartType
                            $.ajax({
                                type: 'POST',
                                url: '/carts/session',
                                data: {
                                    type: type,
                                    _token: '{{ csrf_token() }}',
                                },
                                success: function(response) {
                                    // Handle response
                                    if (response.message == 'success') {
                                        // Trigger reload
                                        filterMeals();
                                    }
                                }
                            });
                        })
                    }
                })();
            }

            //--------------------------------------
            // Display & search menu
            //--------------------------------------
            function filterMeals() {
                var selectedCategory = $('#categorySelect').val();
                var sortSelect = $('#sortSelect').val();
                var inputQuery = $('#search-menu').val();

                $.ajax({
                    url: '/menu',
                    type: 'GET',
                    data: {
                        category: selectedCategory,
                        sort: sortSelect,
                        query: inputQuery,
                    },
                    success: function(response) {
                        $('body').html(response);

                        $('#categorySelect').val(selectedCategory);
                        $('#sortSelect').val(sortSelect);
                        $('#search-menu').val(inputQuery);
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            }

            $('#categorySelect, #sortSelect').off('change').on('change', function() {
                filterMeals();
            });

            $('#search-icon').off('click').on('click', function() {
                filterMeals();
            });

            $('input[name="search-menu"]').on('input', function() {
                const input = $(this).val();
                const searchInput = $(this);
                const crossIcon = $('#cross-icon');

                if (input !== '') {
                    crossIcon.show();
                    searchInput.css({
                        'padding-right': '3.7rem',
                        'important': 'true'
                    });
                } else {
                    crossIcon.hide();
                    searchInput.css('padding-right', '');
                }
            });

            $('#cross-icon').on('click', function() {
                const searchInput = $('input[name="search-menu"]')
                const crossIcon = $(this);

                searchInput.val('');
                // Trigger reload
                filterMeals();
            });

            if ($('input[name="search-menu"]').val() !== '') {
                $('#cross-icon').show();
            }

            $('input[name="search-menu"]').focus(function() {
                // Enable the keydown event
                $(window).on('keydown', function(event) {
                    if (event.keyCode == 13) {
                        event.preventDefault();
                        filterMeals();
                        return false;
                    }
                });
            }).blur(function() {
                // Disable the keydown event
                $(window).off('keydown');
            });
        });
    </script>
@endsection
