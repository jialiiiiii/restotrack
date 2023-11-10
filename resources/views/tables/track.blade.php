@extends('homelayout')
@section('title', 'Track')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .title-blue {
            padding-right: 70px;
        }

        .box {
            width: 70px;
            height: 30px;
            background: #ff4444;
            border-radius: 5px;
            display: flex;
            justify-content: space-evenly;
            align-items: center;
        }

        .text {
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
            color: #fff;
            padding-right: 5px;
        }

        .circle {
            position: relative;
            width: 25px;
            height: 25px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .circle::before {
            content: '';
            position: absolute;
            width: 50%;
            height: 50%;
            border-radius: 50%;
            background-color: #fff;
        }

        .circle::after {
            content: '';
            position: absolute;
            width: 50%;
            height: 50%;
            border-radius: 50%;
            background-color: #fff;
            animation: animate ease-in 1s infinite;
        }

        @keyframes animate {
            from {
                opacity: 0.8;
            }

            to {
                opacity: 0;
                width: 100%;
                height: 100%;
            }
        }

        table {
            background: transparent !important;
        }

        table td {
            background: transparent !important;
        }

        table td:not(.item-empty):not(:has(div.image img)) {
            background: #764A3D !important;
            color: #fff;
        }

        table td:not(:has(div.image img)) {
            cursor: context-menu !important;
        }

        table td:has(div.image img) {
            border-color: #e7d7c6 !important;
        }

        span.dot.green::before {
            content: '';
            display: block;
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: rgba(85, 243, 64, 0.6);
        }

        span.dot.green::after {
            content: '';
            display: block;
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: rgba(85, 243, 64, 0.6);
            animation: live ease-in 1s infinite;
        }

        @keyframes live {
            0% {
                transform: scale(1, 1);
            }

            100% {
                transform: scale(3, 3);
                background-color: rgba(85, 243, 64, 0);
            }
        }
    </style>
@endsection

@section('body')
    <div class="body">
        <div class="d-flex flex-column mx-auto">

            <div class="d-flex justify-content-center align-items-center mb-3">
                <div class="box me-2">
                    <div class="circle"></div>
                    <div class="text">LIVE</div>
                </div>
                <p class="h2 fw-bold title-blue text-center mb-0">Track</p>
            </div>

            @php
                $row = 0;
                $tableCounter = 0;
            @endphp

            <div class="table-responsive" id="content-pd">
                <table class="table mb-4">
                    @foreach ($tables as $table)
                        @php
                            // Check if status is predefined
                            $is_predefined = in_array($table->status, getStatus()) && $table->seat > 0;
                        @endphp

                        @if ($table->row > $row)
                            @if ($row != 0)
                                {{-- Close previous row --}}
                                </tr>
                            @endif

                            {{-- Start new row --}}
                            <tr>
                                @php $row++; @endphp
                        @endif

                        <td class="text-center align-middle @if ($table->status == null) item-empty @endif"
                            id="{{ $table->id }}">

                            <div class="d-flex justify-content-center">
                                {{-- Show seat no only when status is predefined as above --}}

                                <div class="image">
                                    @if ($is_predefined)
                                        <img class="item-img" src="/img/tables/seat-{{ $table->seat }}.png" />
                                        @php $tableCounter++; @endphp
                                    @endif
                                </div>

                                <div class="status">
                                    @if ($is_predefined)
                                        <span class="dot {{ getColorForStatus($table->status) }}"></span>
                                    @else
                                        {{ ucfirst($table->status) }}
                                    @endif
                                </div>
                            </div>
                        </td>
                    @endforeach
                    </tr>
                </table>
            </div>
        </div>
    </div>

    @vite('resources/js/app.js')
    <script>
        $(document).ready(function() {
            // Adjust empty td width
            const inputWidth = $('td:has(div.image img)').width();
            $('td.item-empty div.status').css('width', inputWidth);

            // Adjust table display
            var othersTd = $('td:not(.item-empty):not(:has(div.image img))');
            var tdByGroup = {};

            // Link related td
            othersTd.each(function() {
                var td = $(this);
                var index = td.index();
                var status = td.find('div.status').text().trim();

                var top = td.parent().prev().children().eq(index);
                if (top && top.find('div.status').text().trim() === status) {
                    td.css('box-shadow', (td.css('box-shadow') || '') + ', 0 -25.5px #764A3D');
                }

                var bottom = td.parent().next().children().eq(index);
                if (bottom && bottom.find('div.status').text().trim() === status) {
                    td.css('box-shadow', (td.css('box-shadow') || '') + ', 0 25.5px #764A3D');
                }

                var left = td.prev();
                if (left && left.find('div.status').text().trim() === status) {
                    td.css('box-shadow', (td.css('box-shadow') || '') + ', -25.5px 0 #764A3D');
                }

                var right = td.next();
                if (right && right.find('div.status').text().trim() === status) {
                    td.css('box-shadow', (td.css('box-shadow') || '') + ', 25.5px 0 #764A3D');
                }

                // Grouping
                var group = index;

                for (var no in tdByGroup) {
                    if (tdByGroup.hasOwnProperty(no)) {
                        var items = tdByGroup[no];

                        items.forEach(function(item) {
                            var itemStatus = item.status;
                            var itemTd = item.td;

                            if (itemStatus === status &&
                                (itemTd.get(0) === top.get(0) || itemTd.get(0) === bottom.get(0) ||
                                    itemTd.get(0) === left.get(0) || itemTd.get(0) === right.get(0))
                            ) {
                                group = no;
                            }
                        });
                    }
                }

                tdByGroup[group] = tdByGroup[group] || [];

                tdByGroup[group].push({
                    status: status,
                    td: td
                });
            });

            // Display status at center
            for (var no in tdByGroup) {
                if (tdByGroup.hasOwnProperty(no)) {
                    var items = tdByGroup[no];

                    // If the group is not empty
                    if (items.length > 0) {

                        var first = $(items[0].td);
                        var last = $(items[items.length - 1].td);

                        var top = first.offset().top;
                        var left = first.offset().left;
                        var bottom = last.offset().top + last.innerHeight();
                        var right = last.offset().left + last.innerWidth();

                        items.forEach(function(item, index) {
                            var td = $(item.td);
                            var statusDiv = td.find('div.status');

                            if (index == 0) {
                                // Calculate the center position
                                var centerX = (left + right) / 2;
                                var centerY = (top + bottom) / 2;

                                // Calculate the position offsets
                                var offsetX = centerX - td.offset().left - statusDiv.innerWidth() / 2;
                                var offsetY = centerY - td.offset().top - statusDiv.innerHeight() / 2;

                                // Set the position
                                statusDiv.css({
                                    position: 'absolute',
                                    left: offsetX + 'px',
                                    top: offsetY + 'px',
                                    zIndex: '10'
                                });
                            } else {
                                statusDiv.text('');
                            }
                        });
                    }
                }
            }

            // Make tables clickable
            var tablesTd = $('td:has(div.image img)');

            tablesTd.css('cursor', 'pointer');
            tablesTd.attr('title', 'Click to view table');

            tablesTd.on('click', function() {
                var id = $(this).attr('id');

                // To do
            });
        });
    </script>
@endsection
