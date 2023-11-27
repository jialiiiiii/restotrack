@extends('mgmtlayout')
@section('title', 'Reservations')

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

        .reserveBar {
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
    <form>
        <div class="input-group w-25 mb-4 ms-auto">
            <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control search-bar" placeholder="Date, Time, Pax" name="search" maxlength="50"
                value="{{ $query }}" aria-describedby="basic-addon1">
            <span id="clear-icon" class="clear-icon"><i class="fas fa-times"></i></span>
        </div>
    </form>

    <div id="result" class="d-flex flex-column mb-4">
        @php
            $all = $type == 'all' ? 'checked' : '';
            $today = $type == 'today' ? 'checked' : '';
            $past = $type == 'past' ? 'checked' : '';
            $future = $type == 'future' ? 'checked' : '';
        @endphp

        <div class="d-flex justify-content-center mb-4">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="type" value="all" id="all"
                    {{ $all }}>
                <label class="form-check-label" for="all">
                    All
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="type" value="today" id="today"
                    {{ $today }}>
                <label class="form-check-label" for="today">
                    Today
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="type" value="past" id="past"
                    {{ $past }}>
                <label class="form-check-label" for="past">
                    Past
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="type" value="future" id="future"
                    {{ $future }}>
                <label class="form-check-label" for="future">
                    Future
                </label>
            </div>
        </div>

        <div class="empty-result small text-muted my-2">
            Showing {{ count($reservations) }} result{{ count($reservations) > 1 ? 's' : '' }}
        </div>

        @foreach ($reservations as $r)
            @if ($r->datetime->isToday())
                <div class="submitContainer">
                    <button class="btn btn-secondary submit" id="{{ $r->id }}">Assign Table</button>
                </div>
            @endif
            <div class="d-flex flex-row reserveBox my-2" data-bs-toggle="collapse"
                data-bs-target="#reservation{{ $r->id }}">
                <div class="col-2 fw-bold reserveBar">
                    <div>
                        Reservation #{{ $r->id }}
                    </div>
                </div>
                <div class="col-10">
                    <div class="status fw-bold text-brown">
                        By {{ $r->order->user_name }}


                    </div>
                    <div class="row collapse" id="reservation{{ $r->id }}">
                        <div class="col-12 mt-2">
                            <small class="">{{ $r->datetime->format('d/m/Y h:i A') }}</small>
                        </div>
                        <div class="col-12 my-2">
                            <small class="">{{ $r->pax }} pax</small>
                        </div>
                        @php
                            $total = 0;
                        @endphp
                        @foreach ($r->order->orderMeals as $om)
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
                                Created at {{ $r->created_at->format('d/m/Y h:i A') }}
                            </small>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">
                                Updated at {{ $r->updated_at->format('d/m/Y h:i A') }}
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

                    $.ajax({
                        url: '/tables/active',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(results) {
                            if (results.length > 0) {

                                console.log(results);

                                // Create form
                                var form = '<div class="row mx-auto mb-2 input-group">';
                                form +=
                                    '<label class="input-group-text col-3" for="table">Table</label>';
                                form += '<select class="form-select col" id="table" name="table">';
                                results.forEach(function(r) {
                                    form += '<option value="' + r.id + '">' + r.id +
                                        ' - ' + r.seat + ' seats</option>';
                                });
                                form += '</select></div>';

                                // Popup form
                                Swal.fire({
                                    title: 'Arrange Table',
                                    html: form,
                                    showCancelButton: true,
                                    cancelButtonText: 'Cancel',
                                    confirmButtonText: 'Done',
                                    confirmButtonColor: '#3085d6',
                                    reverseButtons: true,
                                    preConfirm: function() {
                                        return {
                                            table: $('#table').val(),
                                        };
                                    },
                                }).then(function(result) {
                                    if (result.isConfirmed) {
                                        var table = result.value.table;

                                        // Post data
                                        $.ajax({
                                            url: '/reservations/assign',
                                            method: 'POST',
                                            data: {
                                                id: id,
                                                table: table,
                                                _token: '{{ csrf_token() }}',
                                            },
                                            success: function(response) {
                                                // Display message
                                                if (response.message ==
                                                    'success') {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Successful',
                                                        text: 'Table has been assigned to the reservation.',
                                                        confirmButtonText: 'OK',
                                                        confirmButtonColor: '#3085d6',
                                                    }).then(function(
                                                        result) {
                                                        reload();
                                                    });
                                                }
                                            },
                                        });
                                    }
                                });

                            } else {
                                // No tables found
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'No Tables Found',
                                    text: 'There are no tables available at the moment.',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK',
                                });
                            }
                        },
                    });
                });

                $('input[type=checkbox]').change(function() {
                    if (this.checked) {
                        var type = this.value;

                        $.ajax({
                            url: window.location.href,
                            method: 'GET',
                            data: {
                                type: type
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
