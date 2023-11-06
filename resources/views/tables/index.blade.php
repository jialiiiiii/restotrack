@extends('mgmtlayout')
@section('title', 'Tables')

@section('head')
    <style>
        span#table-count {
            background: #1F579C;
            color: #fff;
            cursor: context-menu;
            user-select: auto;
        }

        table {
            width: 0 !important;
            margin-left: auto;
            margin-right: auto;
            border-collapse: separate;
            border-spacing: 50px;
            background: #adbac9 !important;
        }

        td {
            height: 150px !important;
            position: relative;
            border: none;
            background: #fff !important;
            border-width: 3px !important;
            border-style: solid;
            border-color: transparent;
            transition: border-color 0.3s ease;
        }

        td.item-empty {
            background: #ddd !important;
        }

        img.item-img {
            width: 125px;
            background: transparent;
            /* margin-bottom: 1rem; */
        }

        div.status span {
            position: absolute;
            right: -10px;
            bottom: -10px;
            height: 30px;
            width: 30px;
            border-radius: 50%;
            display: inline-block;
            border-width: 3px;
            border-style: solid;
        }

        span.green {
            background: #55f340;
            border-color: #23d30c;
        }

        span.yellow {
            background: #fad53b;
            border-color: #dcb619;
        }

        span.red {
            background: #ff4444;
            border-color: #eb1515;
        }

        span.gray {
            background: #b9b9b9;
            border-color: #9c9c9c;
        }
    </style>
@endsection

@section('body')
    <a href="/tables/arrange" class="btn btn-secondary mb-4" role="button">
        <i class="fas fa-random"></i>&nbsp; Arrange tables
    </a>

    <div class="d-flex justify-content-center mb-4">
        <span id="table-count" class="btn mx-auto"></span>
    </div>

    @php
        $status = ['occupied', 'available', 'reserved', 'out of service'];

        function getColorForStatus($status)
        {
            switch ($status) {
                case 'occupied':
                    return 'red';
                case 'available':
                    return 'green';
                case 'reserved':
                    return 'yellow';
                case 'out of service':
                    return 'gray';
                default:
                    return '';
            }
        }

        $row = 0;
        $tableCounter = 0;
    @endphp

    <div class="table-responsive">

        <table class="table mb-4">

            @foreach ($tables as $table)
                @php
                    // Check if status is predefined
                    $is_predefined = in_array($table->status, $status);
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

                    <div class="d-flex flex-column">
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

    <script>
        $(document).ready(function() {
            $('#table-count').text('{{ $tableCounter }} tables');

            var tablesTd = $('td:has(div.image img)');

            tablesTd.css('cursor', 'pointer');

            tablesTd.on('click', function() {
                var id = $(this).attr('id');

                window.location.href = '/tables/' + id;
            });

        });
    </script>
@endsection
