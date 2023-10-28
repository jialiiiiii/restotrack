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

        .flex-row {
            display: flex;
            justify-content: space-between;
        }

        .flex-row>div {
            flex: 1;
            margin-right: 20px;
            margin-bottom: 20px;
            border: 1px solid black;
            padding: 5px;
        }
    </style>
@endsection

@section('body')
    <a href="/" class="btn btn-secondary mb-4" role="button">
        <i class="fas fa-random"></i>&nbsp; Arrange tables
    </a>

    <h6 class="d-block fw-bold title-blue">{{ count($tables) }} tables</h6>

    <table class="table table-bordered">
        @php
            $status = ["occupied", "available", "reserved", "out of service"];
            $row = 0;
        @endphp
        
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

            @php
                $col_span = 1;
                for ($j = $i + 1; $j < count($tables) && $tables[$j]->row == $table->row && $tables[$j]->col == $table->col; $j++){
                    $col_span++;
                }
            @endphp

            {{-- if status is not predefined as above, seat will be the rowspan --}}
            <td colspan="{{ $col_span }}" @if(!$is_predefined) rowspan="{{ $table->seat }}" @endif>
                {{-- show seat no only when status is predefined as above --}}
                @if ($is_predefined)
                    ({{ $table->seat }})
                @endif

                {{ $table->status }}
            </td>
        @endforeach
    </table>

    <script>
        $(document).ready(function() {


        });
    </script>
@endsection
