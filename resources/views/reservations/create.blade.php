@extends('homelayout')
@section('title', 'Reservation')

@section('head')
    <style>
        @media only screen and (max-width: 700px) {
            .w-75 {
                width: 90% !important;
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
            <p class="h2 fw-bold title-blue text-center">Reserve</p>

            @if ($cartReserve)
                <div class="d-flex justify-content-between mx-3 mx-xl-4 my-3">
                    @if (session()->has('cartReserve'))
                        <a href="/carts" class="btn btn-secondary" role="button">
                            <i class="fas fa-arrow-left"></i>&nbsp; Back to Cart
                        </a>
                    @endif
                    <a href="#" class="btn btn-success ms-auto reserve" role="button">
                        Reserve &nbsp;<i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            @endif

            @if ($message == 'reserve')
                <form class="container-fluid my-3" method="post" action="/reservations" id="reserveForm">
                    @csrf

                    <div class="row w-50 mx-auto mb-2 input-group">
                        <label class="input-group-text col-3" for="date">Date</label>
                        <input id="date" name="date" class="form-control" type="date"
                            onfocus="disableSundays()" />
                    </div>
                    <div class="row w-50 mx-auto mb-2 input-group">
                        <label class="input-group-text col-3" for="time">Time</label>
                        <input id="time" name="time" class="form-control" type="time"
                            onfocus="disableInvalidTimes()" />
                    </div>
                    <div class="row w-50 mx-auto mb-2 input-group">
                        <label class="input-group-text col-3" for="pax">Pax</label>
                        <select class="form-select col" id="pax" name="pax">
                            <option value="1" selected>1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                        </select>
                    </div>
                </form>

                <div class="container-fluid my-4">
                    @php
                        $total = 0;
                        $quantity = 0;
                    @endphp

                    @if (empty($results))
                        <div class="d-flex justify-content-center mx-3 mx-xl-4 my-4">
                            <a href="/reservations/session" class="btn btn-secondary checkout" role="button">
                                Browse Menu
                            </a>
                        </div>
                    @else
                        @foreach ($results as $r)
                            <div class="row justify-content-center px-1 px-md-3 px-xl-5" id="{{ $r->id }}">
                                {{-- Image --}}
                                <div class="col-3 col-lg-2">
                                    <img src="/img/meals/{{ $r->meal_id }}.png" alt="Meal Image" class="w-100">
                                </div>
                                {{-- Name & Description --}}
                                <div class="col-7 col-lg-6">
                                    <div class="d-flex flex-column">
                                        <div class="fw-bold mb-2">
                                            {{ $r->meal_name }}
                                        </div>
                                        <div class="desc">
                                            {{ $r->meal_desc }}
                                        </div>
                                    </div>
                                </div>
                                {{-- Quantity --}}
                                <div class="col-2 col-lg-1 col-xl-2 text-end text-lg-center">
                                    x {{ $r->quantity }}
                                </div>
                                {{-- Price --}}
                                <div class="col-12 col-lg-3 col-xl-2 mt-3 mt-md-0">
                                    <div class="text-end price">
                                        @php
                                            $price = $r->meal_price * $r->quantity;
                                            $discountPrice = $r->meal_sales > 0 ? ($price * (100 - $r->meal_sales)) / 100 : $price;
                                            $total += $discountPrice;

                                            $quantity += $r->quantity;
                                        @endphp

                                        @if ($r->meal_sales > 0)
                                            <span class="text-decoration-line-through me-1">RM
                                                {{ number_format($price, 2) }}</span>
                                        @endif

                                        RM {{ number_format($discountPrice, 2) }}
                                    </div>
                                </div>
                            </div>
                            <hr class="mx-0 mx-xl-4 my-4">
                        @endforeach

                        <div class="row justify-content-center px-1 px-md-3 px-xl-5 mb-3 text-brown">
                            <div class="col-6 col-lg-6 col-xl-8 order-1 fw-bold text-start h5">Total (RM)</div>
                            <div class="col-12 col-lg-3 col-xl-2 order-3 order-lg-1 fw-bold text-end text-xl-center h5">
                                {{ $quantity }} items
                            </div>
                            <div class="col-6 col-lg-3 col-xl-2 order-2 order-lg-2 fw-bold text-end h5 total">
                                RM {{ number_format($total, 2) }}
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="col-12 mx-auto mt-3 mb-5 d-flex flex-column align-items-center">
                    <div class="mb-4">
                        1 point earned for every RM1 spent
                    </div>
                    <img class="img-empty w-25" src="/img/empty-reserve.png" alt="Empty Reserve" />
                    <div class="d-flex flex-column text-center mt-4 w-75">

                        @if ($message == 'pointsRequired')
                            <span>Earned <b>{{ $points }}</b> more points to reserve your meals.</span>
                        @elseif ($message == 'loginRequired')
                            Register / login to earn your points now.
                            <div class="d-flex justify-content-between mt-4 mx-auto w-75">
                                <a href="/customers/register" class="btn btn-secondary" role="button">
                                    <i class="fas fa-arrow-left"></i>&nbsp; Register
                                </a>
                                <a href="/login" class="btn btn-secondary" role="button">
                                    Login &nbsp;<i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i
                    class="fas fa-arrow-up"></i></a>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.reserve').on('click', function(e) {
                e.preventDefault();

                if ({{ count($results) }} <= 0) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Your cart is empty.",
                        confirmButtonColor: '#3085d6',
                    });

                    return false;
                }

                var date = $('#date').val();
                var time = $('#time').val();
                var pax = $('#pax').val();

                if (validateDateTime(date, time)) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Make Reservation',
                        text: 'Are you sure to make the reservation?',
                        showCancelButton: true,
                        cancelButtonText: 'Cancel',
                        confirmButtonText: 'Confirm',
                        confirmButtonColor: '#3085d6',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#reserveForm').submit();
                        }
                    });
                }
            });

            // Set min date
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth() + 1; // January is 0
            var yyyy = today.getFullYear();
            if (dd < 10) {
                dd = '0' + dd;
            }
            if (mm < 10) {
                mm = '0' + mm;
            }
            today = yyyy + '-' + mm + '-' + dd;
            document.getElementById("date").setAttribute("min", today);
        });
    </script>
@endsection
