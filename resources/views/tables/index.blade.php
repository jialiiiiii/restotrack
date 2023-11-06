@extends('mgmtlayout')
@section('title', 'Tables')

@section('head')
    <style>
        h6 {
            font-size: 1.1rem;
            width: fit-content;
            background: #ffca2b;
            border-radius: 5px;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 20px;
            padding: 8px 20px;
        }

        img.table {
            width: 140px;
            background: transparent;
        }

        img.table-unavailable {
            -webkit-filter: grayscale(100%); /* Safari 6.0 - 9.0 */
            filter: grayscale(100%);
        }

        td, th {
            border-width: 50px !important;
            height: 120px !important;
        }

        table {
            width: 0 !important;
            margin-left: auto;
            margin-right: auto;
            background: transparent !important;
            border-color: #f4e1cd !important;
        }

        .table-others {
            background: #416188 !important;
        }
    </style>
@endsection

@section('body')
    <a href="/tables/arrange" class="btn btn-secondary mb-4" role="button">
        <i class="fas fa-random"></i>&nbsp; Arrange tables
    </a>

    @php
        $status = ['occupied', 'available', 'reserved', 'out of service'];

        function getColorForStatus($status) {
            switch ($status) {
                case 'occupied':
                    return 'text-danger';
                case 'available':
                    return 'text-success';
                case 'reserved':
                    return 'text-primary';
                case 'out of service':
                    return 'text-secondary';
                default:
                    return 'text-light';
            }
        }

        $row = 0;
        $tableCounter = 0;
    @endphp

    <h6 id="table_count" class="d-block fw-bold title-blue"></h6>

    <table class="table table-bordered mb-4">
        
        @foreach ($tables as $i => $table)
            @php
                // check if status is predefined
                $is_predefined = in_array($table->status, $status);

            @endphp

            @if ($table->row > $row)
                @if ($row != 0)
                    {{-- close previous row --}}
                    </tr>
                @endif

                {{-- start new row --}}
                <tr>
                @php $row++; @endphp
            @endif

            {{-- if status is not predefined as above, seat will be the rowspan --}}
            <td class="text-center align-middle @if (!$is_predefined && $table->status != null) table-others @endif" rowspan="{{ $table->rowspan }}" colspan="{{ $table->colspan }}">
                <div class="d-flex flex-column">
                    {{-- show seat no only when status is predefined as above --}}
                    @if ($is_predefined)
                        <div>
                            <img class="table @if ($table->status != 'available') table-unavailable @endif" src="/img/tables/seat-{{ $table->seat }}.png" />
                        </div>
                        @php $tableCounter++; @endphp
                    @endif

                    <div class="{{ getColorForStatus($table->status) }} text-wrap">
                        @if ($is_predefined) 
                            <i class="fas fa-circle"></i>
                        @endif
                        {{ ucfirst($table->status) }}
                    </div>
                </div>
            </td>
        @endforeach
        </tr>
    </table>

    <script>
        $(document).ready(function() {
            $('#table_count').text('{{ $tableCounter }} tables');
        });
    </script>
@endsection
