@extends('homelayout')
@section('title', 'Reservation')

@section('head')
    <style>
        .orderBar {
            cursor: pointer;
        }
    </style>
@endsection

@section('body')
    @if (session()->has('msg'))
        <script>
            @if (session()->get('msg') == 'reservationMade')
                Swal.fire({
                    icon: 'success',
                    title: 'Successful',
                    text: 'Your reservation has been made.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                })
            @endif
        </script>
    @endif

    <div class="body">
        <div class="d-flex flex-column mx-auto mb-4">
            <p class="h2 fw-bold title-blue text-center">Reservations</p>

            @forelse ($reservations as $r)
                <div class="d-flex flex-column container-md reserveBox my-3">
                    <div class="row fw-bold reserveBar" data-bs-toggle="collapse"
                        data-bs-target="#reservation{{ $r->id }}" aria-expanded="false"
                        aria-controls="reservation{{ $r->id }}" role="button" href="#reservation{{ $r->id }}">
                        <div class="col-10">
                            Reservation #{{ $r->id }}
                        </div>
                        <div class="col-2 text-end pe-3 my-auto">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="row collapse mt-2" id="reservation{{ $r->id }}">
                        <div class="col-12 mt-2">
                            <small class="text-muted datetime">
                                {{ $r->datetime->format('d/m/Y h:i A') }}
                            </small>
                        </div>
                        <div class="col-12 mt-2">
                            <small class="text-muted pax">
                                {{ $r->pax }} pax
                            </small>
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
                        <div class="col-12 text-center text-sm-end">
                            <button class="btn btn-sm btn-danger mx-2 me-sm-0 my-1 cancel" data-id="{{ $r->id }}">Cancel
                                Reservation</button>
                            <button class="btn btn-sm btn-secondary mx-2 me-sm-0 update" data-id="{{ $r->id }}">Update
                                Reservation</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 mx-auto my-5 d-flex flex-column align-items-center">
                    <img class="img-empty w-25" src="/img/empty-result.png" alt="Empty Reservation" />
                    <div class="mt-4">
                        No reservations found.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        $(document).ready(function() {

            $('.update').click(function() {
                var id = $(this).data('id');
                var paxText = $(this).parents('.row').find('.pax').text().trim();
                var datetime = $(this).parents('.row').find('.datetime').text().trim();
                var datetimeParts = datetime.split(' ');

                // Format date
                var initialDate = datetimeParts[0];
                var [day, month, year] = initialDate.split('/');
                var formattedDate = `${year}-${month}-${day}`;

                // Format time
                var initialTime = datetimeParts.slice(1).join(' ');
                var [hoursMinutes, meridian] = initialTime.split(' ');
                var [hours, minutes] = hoursMinutes.split(':');
                if (meridian === 'PM' && hours < 12) {
                    hours = parseInt(hours, 10) + 12;
                }
                var formattedTime = `${hours}:${minutes}`;

                // Format pax
                var match = paxText.match(/(\d+)/);
                var initialPax = match ? parseInt(match[0], 10) : null;

                // Popup form
                Swal.fire({
                    title: 'Change Date Time',
                    html: `
                        <div class="row mx-auto mb-2 input-group">
                            <label class="input-group-text col-3" for="date">Date</label>
                            <input id="date" name="date" class="form-control" type="date" onfocus="disableSundays()" value="${formattedDate}" />
                        </div>
                        <div class="row mx-auto mb-2 input-group">
                            <label class="input-group-text col-3" for="time">Time</label>
                            <input id="time" name="time" class="form-control" type="time" onfocus="disableInvalidTimes()" value="${formattedTime}" />
                        </div>
                        <div class="row mx-auto mb-2 input-group">
                        <label class="input-group-text col-3" for="pax">Pax</label>
                        <select class="form-select col" id="pax" name="pax">
                            <option value="1">1</option>
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
                    `,
                    showCancelButton: true,
                    cancelButtonText: 'Cancel',
                    confirmButtonText: 'Done',
                    confirmButtonColor: '#3085d6',
                    reverseButtons: true,
                    preConfirm: function() {
                        return {
                            date: $('#date').val(),
                            time: $('#time').val(),
                            pax: $('#pax').val(),
                        };
                    },
                    didOpen: function() {
                        $('#pax').val(initialPax);
                    }
                }).then(function(result) {
                    if (result.isConfirmed) {
                        var date = result.value.date;
                        var time = result.value.time;
                        var pax = result.value.pax;

                        if (validateDateTime(date, time)) {
                            // Check if date time changed
                            if (date !== formattedDate || time !== formattedTime || parseInt(pax,
                                    10) !== initialPax) {
                                // Post data
                                $.ajax({
                                    type: 'PUT',
                                    url: '/reservations/' + id,
                                    data: {
                                        pax: pax,
                                        date: date,
                                        time: time,
                                        _token: '{{ csrf_token() }}',
                                    },
                                    success: function(response) {
                                        if (response.message == 'success') {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Successful',
                                                text: 'Your reservation is updated.',
                                                confirmButtonText: 'OK',
                                                confirmButtonColor: '#3085d6',
                                            }).then(function(result) {
                                                reload();
                                            });
                                        }
                                    },
                                });
                            } else {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'No Changes Made',
                                    text: 'Your reservation remain unchanged.',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#3085d6',
                                })
                            }
                        }
                    }
                });

            });

            $('.cancel').click(function() {
                var id = $(this).data('id');

                Swal.fire({
                    icon: 'info',
                    title: 'Cancel Reservation',
                    text: 'Are you sure to cancel the reservation?',
                    showCancelButton: true,
                    cancelButtonText: 'No, Cancel',
                    confirmButtonText: 'Yes, Confirm',
                    confirmButtonColor: '#3085d6',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'DELETE',
                            url: '/reservations/' + id,
                            data: {
                                _token: '{{ csrf_token() }}',
                            },
                            success: function(response) {
                                if (response.message == 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Successful',
                                        text: 'Your reservation is cancelled.',
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
