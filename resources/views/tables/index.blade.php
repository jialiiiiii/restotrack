@extends('mgmtlayout')
@section('title', 'Tables')

@section('head')
    <style>
        td:not(:has(span.dot)) {
            cursor: context-menu !important;
        }
    </style>
@endsection

@section('body')
    <div id="content-pd">
        <a href="/tables/arrange" class="btn btn-secondary mb-4" role="button">
            <i class="fas fa-random"></i>&nbsp; Arrange tables
        </a>

        <div class="d-flex justify-content-center mb-4">
            <span id="table-count" class="btn mx-auto"></span>
        </div>

        @php
            $row = 0;
            $tableCounter = 0;
        @endphp

        <div class="table-responsive">

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

    <script>
        $(document).ready(function() {
            $('#table-count').text('{{ $tableCounter }} tables');

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
                    td.css('box-shadow', (td.css('box-shadow') || '') + ', 0 -25.5px #fff');
                }

                var bottom = td.parent().next().children().eq(index);
                if (bottom && bottom.find('div.status').text().trim() === status) {
                    td.css('box-shadow', (td.css('box-shadow') || '') + ', 0 25.5px #fff');
                }

                var left = td.prev();
                if (left && left.find('div.status').text().trim() === status) {
                    td.css('box-shadow', (td.css('box-shadow') || '') + ', -25.5px 0 #fff');
                }

                var right = td.next();
                if (right && right.find('div.status').text().trim() === status) {
                    td.css('box-shadow', (td.css('box-shadow') || '') + ', 25.5px 0 #fff');
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

                window.location.href = '/tables/' + id;
            });

        });
    </script>
@endsection
