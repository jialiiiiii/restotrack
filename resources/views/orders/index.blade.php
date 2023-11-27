@extends('mgmtlayout')
@section('title', 'Orders')

@section('head')
    <style>
        .form-check {
            display: flex;
            justify-content: center;
            align-items: end;
            min-width: 10%;
        }

        .form-check-input {
            width: 1.4em;
            height: 1.4em;
        }

        .form-check-label {
            margin-left: 8px;
        }

        .status {
            margin-top: 2px;
            display: flex;
            justify-content: space-between;
            padding: .375rem 0;
        }

        .empty-result {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .orderBar {
            padding: .375rem .75rem;
        }

        .submitContainer {
            position: relative;
        }

        .submit {
            position: absolute;
            right: 18px;
            top: 28px;
        }
    </style>
@endsection

@section('body')
    @if (session()->has('msg') && session()->get('msg') == 'pointsEarned')
        <script>
            Swal.fire({
                icon: 'success',
                title: '{{ session()->get('point') }}' + ' Points Earned',
                text: 'Customer may check their email for receipt.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
            })
        </script>
    @endif

    <form>
        <div class="input-group w-25 mb-4 ms-auto">
            <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control search-bar" placeholder="Name, Price, Quantity" name="search"
                maxlength="50" value="{{ $query }}" aria-describedby="basic-addon1">
            <span id="clear-icon" class="clear-icon"><i class="fas fa-times"></i></span>
        </div>
    </form>

    <div id="result" class="d-flex flex-column mb-4">
        @php
            $all = $status == 'all' ? 'checked' : '';
            $pending = $status == 'pending' ? 'checked' : '';
            $preparing = $status == 'preparing' ? 'checked' : '';
            $served = $status == 'served' ? 'checked' : '';
            $paid = $status == 'paid' ? 'checked' : '';
            $reserved = $status == 'reserved' ? 'checked' : '';
            $cancelled = $status == 'cancelled' ? 'checked' : '';
        @endphp

        <div class="d-flex justify-content-center mb-4">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="status" value="all" id="all"
                    {{ $all }}>
                <label class="form-check-label" for="all">
                    All
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="status" value="pending" id="pending"
                    {{ $pending }}>
                <label class="form-check-label" for="pending">
                    Pending
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="status" value="preparing" id="preparing"
                    {{ $preparing }}>
                <label class="form-check-label" for="preparing">
                    Preparing
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="status" value="served" id="served"
                    {{ $served }}>
                <label class="form-check-label" for="served">
                    Served
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="status" value="paid" id="paid"
                    {{ $paid }}>
                <label class="form-check-label" for="paid">
                    Paid
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="status" value="reserved" id="reserved"
                    {{ $reserved }}>
                <label class="form-check-label" for="reserved">
                    Reserved
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="status" value="cancelled" id="cancelled"
                    {{ $cancelled }}>
                <label class="form-check-label" for="cancelled">
                    Cancelled
                </label>
            </div>
        </div>

        <div class="empty-result small text-muted my-2">
            Showing {{ count($orders) }} result{{ count($orders) > 1 ? 's' : '' }}
        </div>

        @foreach ($orders as $o)
            @php
                $next = ucfirst(getNextStatus($o->status));
            @endphp
            @if (!empty($next))
                <div class="submitContainer">
                    <button class="btn btn-secondary submit" id="{{ $o->id }}">{{ $next }}</button>
                </div>
            @endif

            <div class="d-flex flex-row orderBox my-2" data-bs-toggle="collapse"
                data-bs-target="#order{{ $o->id }}">
                <div class="col-2 fw-bold orderBar">
                    <div>
                        Order #{{ $o->id }}
                    </div>
                </div>
                <div class="col-10">
                    <div class="status fw-bold">
                        <div class="my-auto">
                            <span class="dot small ms-0 {{ getColorForOrderStatus($o->status) }}"></span>
                            {{ ucfirst($o->status) }}
                        </div>
                    </div>
                    <div class="row collapse" id="order{{ $o->id }}">
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
                        <div class="col-12 mt-2">
                            <small class="text-muted">
                                @if ($o->type == 'dine-in')
                                    {{ ucfirst($o->type) }}
                                    @if ($o->status != 'reserved')
                                        at Table {{ $o->table_id }}
                                    @endif
                                @else
                                    {{ ucfirst($o->type) }}
                                @endif
                            </small>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">
                                Created at {{ $o->created_at->format('d/m/Y h:i A') }}
                            </small>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">
                                Updated at {{ $o->updated_at->format('d/m/Y h:i A') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Receive broadcasts --}}
        @vite('resources/js/app.js')

        <script>
            $(document).ready(function() {

                $('button.submit').click(function() {
                    var id = $(this).attr('id');
                    var status = $(this).text().toLowerCase();

                    $.ajax({
                        type: 'PUT',
                        url: '/orders/' + id,
                        data: {
                            status: status,
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            reload();
                        },
                    });
                });

                $('input[type=checkbox]').change(function() {
                    if (this.checked) {
                        var status = this.value;

                        $.ajax({
                            url: window.location.href,
                            method: 'GET',
                            data: {
                                status: status
                            },
                            success: function(response) {
                                var updated = $(response).find('#result');
                                $('#result').html(updated.html());

                                loadBaseJs();
                            },
                            error: function(xhr, status, error) {
                                console.log(error);
                            }
                        });
                    }
                });
            });
        </script>
    </div>
@endsection
