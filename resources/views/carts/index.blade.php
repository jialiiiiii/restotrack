@extends('homelayout')
@section('title', 'Cart')

@section('head')
    <style>
        .plus,
        .minus {
            cursor: pointer;
        }

        .plus:hover,
        .minus:hover {
            background: #31629f;
        }

        .fa-trash {
            cursor: pointer;
            font-size: 1.4rem;
            color: #eb1515;
        }

        .fa-trash:hover {
            animation: shake 0.82s cubic-bezier(.36, .07, .19, .97) both;
            transform: translate3d(0, 0, 0);
            backface-visibility: hidden;
            perspective: 1000px;
        }

        @keyframes shake {

            10%,
            90% {
                transform: translate3d(-1px, 0, 0);
            }

            20%,
            80% {
                transform: translate3d(1px, 0, 0);
            }

            30%,
            50%,
            70% {
                transform: translate3d(-2px, 0, 0);
            }

            40%,
            60% {
                transform: translate3d(2px, 0, 0);
            }
        }

        .desc {
            display: -webkit-box;
            -webkit-line-clamp: 5;
            -webkit-box-orient: vertical;
            overflow: hidden;
            cursor: context-menu;
        }

        .desc.expand {
            display: block;
        }

        .price,
        .trash {
            padding: .375rem .75rem;
        }

        @media only screen and (max-width: 576px) {
            .fa-trash {
                font-size: 1.2rem;
            }

            .h5 {
                font-size: 1.1rem;
            }
        }

        @media only screen and (max-width: 332px) {
            .h5 {
                font-size: 1.04rem;
            }
        }

        @media only screen and (max-width: 580px) {
            .w-50 {
                width: 100% !important;
            }
        }

        @media only screen and (min-width: 580px) and (max-width: 767px) {
            .w-50 {
                width: 70% !important;
            }
        }
    </style>
@endsection

@section('body')
    <div class="body">
        <div class="d-flex flex-column mx-auto">
            <p class="h2 fw-bold title-blue text-center">Cart</p>

            @php
                $notEmpty = count($results) > 0;
            @endphp

            <div class="d-flex justify-content-between mx-3 mx-xl-4 my-3">
                <a href="/menu" class="btn btn-secondary" role="button">
                    <i class="fas fa-arrow-left"></i>&nbsp; Back to Menu
                </a>
                @if ($notEmpty)
                    @if ($cartTable)
                        <a href="#" class="btn btn-success checkout" role="button">
                            Checkout &nbsp;<i class="fas fa-arrow-right"></i>
                        </a>
                    @else
                        <a href="/reservations/create" class="btn btn-success" role="button">
                            Reservation &nbsp;<i class="fas fa-arrow-right"></i>
                        </a>
                    @endif
                @endif
            </div>

            @if ($notEmpty)
                <div class="container-fluid my-3">
                    @if ($cartTable)
                        <div class="row w-50 mx-auto mb-2 input-group">
                            <label class="input-group-text col-3" for="user">Order by</label>
                            <select class="form-select col" id="user">
                                <option value="{{ session()->getId() }}"
                                    {{ session()->get('cartUser') == 'Guest' ? 'selected' : '' }}>
                                    Guest
                                </option>
                                @if (auth()->guard('customer')->check())
                                    <option value="{{ auth()->guard('customer')->user()->id }}"
                                        {{ session()->get('cartUserId') ==auth()->guard('customer')->user()->id? 'selected': '' }}>
                                        {{ auth()->guard('customer')->user()->name }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="row w-50 mx-auto mb-2 input-group">
                            <label class="input-group-text col-3" for="type">Order for</label>
                            <select class="form-select col" id="type">
                                @if (session()->get('cartTable') != 'ta')
                                    <option value="dine-in" {{ session()->get('cartType') == 'dine-in' ? 'selected' : '' }}>
                                        Dine-In</option>
                                @endif
                                <option value="takeaway" {{ session()->get('cartType') == 'dine-in' ? '' : 'selected' }}>
                                    Takeaway</option>
                            </select>
                        </div>
                    @endif

                    <div class="row justify-content-center px-1 px-md-2 px-xl-5 my-4 my-xl-5 text-brown">
                        <div class="col-0"></div>
                        <div class="col-4 col-md-6 col-xl-7 fw-bold">Item</div>
                        <div class="col-4 col-md-2 fw-bold text-center">Price (RM)</div>
                        <div class="col-4 col-md-3 col-xl-2 fw-bold text-end text-md-center">Quantity</div>
                        <div class="col-0 col-md-1"></div>
                    </div>

                    @php
                        $total = 0;
                        $quantity = 0;
                    @endphp

                    @foreach ($results as $r)
                        <div class="row justify-content-center px-1 px-xl-5" id="{{ $r->id }}">
                            {{-- Image --}}
                            <div class="col-3 col-md-2 order-1">
                                <img src="/img/meals/{{ $r->meal_id }}.png" alt="Meal Image" class="w-100">
                            </div>
                            {{-- Name & Description --}}
                            <div class="col-7 col-md-4 col-xl-5 order-2">
                                <div class="d-flex flex-column">
                                    <div class="fw-bold mb-2">
                                        {{ $r->meal_name }}
                                    </div>
                                    <div class="desc">
                                        {{ $r->meal_desc }}
                                    </div>
                                </div>
                            </div>
                            {{-- Price --}}
                            <div class="col-12 col-md-2 order-4 order-md-3 mt-3 mt-md-0">
                                <div class="text-center price">
                                    @php
                                        $price = $r->meal_price * $r->quantity;
                                        $discountPrice = $r->meal_sales > 0 ? ($price * (100 - $r->meal_sales)) / 100 : $price;
                                        $total += $discountPrice;

                                        $quantity += $r->quantity;
                                    @endphp

                                    @if ($r->meal_sales > 0)
                                        <span
                                            class="text-decoration-line-through me-1">{{ number_format($price, 2) }}</span>
                                    @endif

                                    {{ number_format($discountPrice, 2) }}
                                </div>
                            </div>
                            {{-- Quantity --}}
                            <div class="col-7 col-sm-6 col-md-3 col-xl-2 order-5 order-md-4 ms-auto mt-3 mt-md-0">
                                <div class="input-group w-75 ms-auto mx-md-auto">
                                    <span class="input-group-text minus"><i class="fas fa-minus"></i></span>
                                    <input type="text" class="form-control text-center quantity" data-min="1"
                                        data-max="50" value="{{ $r->quantity }}">
                                    <span class="input-group-text plus"><i class="fas fa-plus"></i></span>
                                </div>
                            </div>
                            {{-- Delete --}}
                            <div class="col-2 col-md-1 order-3 order-md-5">
                                <div class="trash text-end text-xl-center">
                                    <i class="fas fa-trash delete"></i>
                                </div>
                            </div>
                        </div>
                        <hr class="mx-0 mx-xl-4 my-4 my-xl-5">
                    @endforeach

                    <div class="row justify-content-center px-1 px-xl-5 mb-3 text-brown">
                        <div class="col-0"></div>
                        <div class="col-4 col-md-6 col-xl-7 fw-bold text-start h5">Total (RM)</div>
                        <div class="col-4 col-md-2 fw-bold text-center h5 total">{{ number_format($total, 2) }}</div>
                        <div class="col-4 col-md-3 col-xl-2 fw-bold text-end text-md-center h5">{{ $quantity }} items
                        </div>
                        <div class="col-0 col-md-1"></div>
                    </div>
                </div>
            @else
                <div class="col-12 mx-auto my-5 d-flex flex-column align-items-center">
                    <img class="img-empty w-25" src="/img/empty-cart.png" alt="Empty Cart" />
                    <div class="mt-4">
                        Your cart is empty, fill it up now!
                    </div>
                </div>
            @endif

            @if ($cartTable)
                <div class="d-flex justify-content-between mx-3 mx-xl-4 mb-4">
                    <a href="/orders/view" class="btn btn-secondary" role="button">
                        <i class="fas fa-receipt"></i>&nbsp; View Orders
                    </a>
                    @if ($notEmpty)
                        <a href="#" class="btn btn-success checkout" role="button">
                            Checkout &nbsp;<i class="fas fa-arrow-right"></i>
                        </a>
                    @endif
                </div>
            @endif

            <form id="checkoutForm" method="post" action="/orders" class="d-none">
                @csrf
                <input type="hidden" name="userId" value="{{ session()->get('cartUserId') }}">
            </form>

            <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i
                    class="fas fa-arrow-up"></i></a>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var ajaxInProgress = false;

            $('.checkout').on('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    icon: 'info',
                    title: 'Place Order',
                    text: 'Are you sure to place the order?',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel',
                    confirmButtonText: 'Confirm',
                    confirmButtonColor: '#3085d6',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#checkoutForm').submit();
                    }
                });
            });

            $('.plus').off('click');
            $('.plus').on('click', function() {
                if (ajaxInProgress) return;

                var field = $(this).prev('.quantity');
                var max = parseInt(field.data('max'));
                var value = parseInt(field.val()) || 0;

                if (value < max) {
                    field.val(value + 1);

                    // Post data
                    var id = $(this).parents('.row').attr('id');
                    ajaxRequest(id, 'plus');
                }
            });

            $('.minus').off('click');
            $('.minus').on('click', function() {
                if (ajaxInProgress) return;

                var field = $(this).next('.quantity');
                var min = parseInt(field.data('min'));
                var value = parseInt(field.val()) || 0;

                if (value > min) {
                    field.val(value - 1);

                    // Post data
                    var id = $(this).parents('.row').attr('id');
                    ajaxRequest(id, 'minus');
                }
            });

            $(".quantity").off("input");
            $(".quantity").on("input", function() {
                var value = $(this).val();
                var min = parseInt($(this).data('min'));
                var max = parseInt($(this).data('max'));

                var newValue = value.replace(/[^0-9]/g, '');
                var intValue = parseInt(newValue);

                $(this).val(newValue);

                // Check for min
                if (isNaN(intValue) || intValue < min) {
                    intValue = min;
                }
                // Check for max
                if (isNaN(intValue) || intValue > max) {
                    intValue = max;
                }

                // Post data
                var id = $(this).parents('.row').attr('id');
                ajaxRequest(id, 'equal', intValue);
            });

            function ajaxRequest(id, action, value = 1) {
                ajaxInProgress = true;

                $.ajax({
                    type: 'PUT',
                    url: '/carts/' + id,
                    data: {
                        action: action,
                        value: value,
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        if (response.message == 'success') {
                            reload();
                        }
                    },
                    complete: function() {
                        ajaxInProgress = false;
                    },
                });
            }

            $('.delete').off('click');
            $('.delete').on('click', function() {
                var id = $(this).parents('.row').attr('id');

                // Post data
                var id = $(this).parents('.row').attr('id');
                $.ajax({
                    type: 'DELETE',
                    url: '/carts/' + id,
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        // Handle response
                        if (response.message == 'success') {

                            Swal.fire({
                                icon: 'success',
                                title: 'Successful',
                                text: 'Cart item deleted!',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3085d6',
                            }).then((result) => {
                                reload();
                            });
                        }
                    }
                });
            });

            $('.desc').off('click');
            $('.desc').on('click', function() {
                $(this).toggleClass('expand');
            });

            $('#user').on('change', function() {
                var id = $(this).val();
                var user = $("option[value='" + id + "']:selected").text().trim();

                // Change session for cartUser
                $.ajax({
                    type: 'POST',
                    url: '/carts/session',
                    data: {
                        user: user,
                        userId: id,
                        _token: '{{ csrf_token() }}',
                    }
                });
            });

            $('#type').on('change', function() {
                var type = $(this).val();

                // Change session for cartType
                $.ajax({
                    type: 'POST',
                    url: '/carts/session',
                    data: {
                        type: type,
                        _token: '{{ csrf_token() }}',
                    }
                });
            });

        });
    </script>
@endsection
