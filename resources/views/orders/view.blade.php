@extends('homelayout')
@section('title', 'Order')

@section('head')
    <style>
        .orderBar {
            cursor: pointer;
        }

        .swal2-close:focus {
            box-shadow: none !important;
        }
    </style>
@endsection

@section('body')
    @if (session()->has('msg'))
        <script>
            @if (session()->get('msg') == 'orderPlaced')
                Swal.fire({
                    icon: 'success',
                    title: 'Successful',
                    text: 'Your order has been placed, and it will be served to you soon.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                })
            @endif
        </script>
    @endif

    <div class="body">
        <div class="d-flex flex-column mx-auto mb-4">
            <p class="h2 fw-bold title-blue text-center">Orders</p>

            @if (session()->has('cartTable'))
                <div class="d-flex justify-content-between mx-3 mx-xl-4 my-2">
                    <a href="/carts" class="btn btn-secondary" role="button">
                        <i class="fas fa-arrow-left"></i>&nbsp; Back to Cart
                    </a>
                </div>
            @endif

            @forelse ($orders as $o)
                <div class="d-flex flex-column container-md orderBox my-3">
                    <div class="row fw-bold orderBar" data-bs-toggle="collapse" data-bs-target="#order{{ $o->id }}"
                        aria-expanded="{{ $o->status == 'paid' || $o->status == 'cancelled' ? 'false' : 'true' }}"
                        aria-controls="order{{ $o->id }}" role="button" href="#order{{ $o->id }}">
                        <div class="col-10">
                            Order #{{ $o->id }}
                            <div class="status d-inline">
                                <span
                                    class="dot small {{ getColorForOrderStatus($o->status) }}"></span>{{ ucfirst($o->status) }}
                            </div>
                        </div>
                        <div class="col-2 text-end pe-3 my-auto">
                            <i
                                class="fas fa-chevron-{{ $o->status == 'paid' || $o->status == 'cancelled' ? 'down' : 'up' }}"></i>
                        </div>
                    </div>
                    <div class="row collapse {{ $o->status == 'paid' || $o->status == 'cancelled' ? '' : 'show' }}"
                        id="order{{ $o->id }}">
                        <div class="col-12 mt-2">
                            <small class="text-muted">
                                (@if ($o->type == 'dine-in')
                                    {{ ucfirst($o->type) }} at Table {{ $o->table_id }}
                                @else
                                    {{ ucfirst($o->type) }}
                                @endif)
                            </small>
                        </div>
                        @php
                            $total = 0;
                        @endphp
                        @foreach ($o->orderMeals as $om)
                            @php
                                $total += $om->price;
                            @endphp
                            <div class="col-7 col-sm-9 col-lg-10 mt-2">
                                {{ $om->quantity }} x {{ $om->meal->name }}
                            </div>
                            <div class="col-5 col-sm-3 col-lg-2 mt-2 text-end">
                                RM {{ number_format($om->price, 2) }}
                            </div>
                        @endforeach
                        <div class="col-7 col-sm-9 col-lg-10 my-3 fw-bold">
                            Total
                        </div>
                        <div class="col-5 col-sm-3 col-lg-2 my-3 text-end fw-bold">
                            RM {{ number_format($total, 2) }}
                        </div>
                        @if ($o->status == 'pending')
                            <div class="col-12 text-end">
                                <button class="btn btn-sm btn-danger cancel" id="{{ $o->id }}">Cancel Order</button>
                            </div>
                        @elseif ($o->status == 'served')
                            <div class="col-12 text-end">
                                <button class="btn btn-sm btn-secondary pay" id="{{ $o->id }}">Pay Now</button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12 mx-auto my-5 d-flex flex-column align-items-center">
                    <img class="img-empty w-25" src="/img/empty-result.png" alt="Empty Order" />
                    <div class="mt-4">
                        No orders found.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Receive broadcasts --}}
    @vite('resources/js/app.js')

    <script>
        $(document).ready(function() {
            $('.pay').click(function() {
                var id = $(this).attr('id');
                var type = $(this).parent().prevAll('div').find('.text-muted').text().trim();
                type = type.replace(/[()]/g, ''); // Removes parentheses
                var total = $(this).parent().prev('div').text().trim();

                Swal.fire({
                    title: 'Order #' + id,
                    html: type + '<br><br>' + '<div class="text-success fw-bold">' +
                        '<i class="fas fa-wallet"></i></i>&nbsp;' + total +
                        '</div>',
                    showConfirmButton: false,
                    showCloseButton: true,
                    footer: 'Please show this to the cashier at the counter.'
                })
            });

            $('.cancel').click(function() {
                var id = $(this).attr('id');

                Swal.fire({
                    icon: 'info',
                    title: 'Cancel Order',
                    text: 'Are you sure to cancel the order?',
                    showCancelButton: true,
                    cancelButtonText: 'No, Cancel',
                    confirmButtonText: 'Yes, Confirm',
                    confirmButtonColor: '#3085d6',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'PUT',
                            url: '/orders/' + id,
                            data: {
                                status: 'cancelled',
                                _token: '{{ csrf_token() }}',
                            },
                            success: function(response) {
                                if (response.message == 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Successful',
                                        text: 'Your order has been cancelled.',
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#3085d6',
                                    }).then(function(result) {
                                        reload();
                                    });
                                }
                            },
                        });
                    }
                });
            });
        });
    </script>
@endsection
