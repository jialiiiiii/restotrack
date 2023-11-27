@extends('mgmtlayout')
@section('title', 'View Table')

@section('head')
    <style>
        .tableBox {
            position: relative;
            height: 150px;
            width: 150px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            border-width: 3px;
            border-style: solid;
            border-color: transparent;
        }

        td img {
            cursor: pointer;
            width: 40%;
        }
    </style>
@endsection

@section('body')
    <a href="/tables" class="btn btn-secondary mb-4" role="button">
        <i class="fas fa-arrow-left"></i>&nbsp; Back to index
    </a>

    <div class="tableBox text-center align-middle mb-4 mx-auto">
        <div class="image">
            <img class="item-img" src="/img/tables/seat-{{ $t->seat }}.png" />
        </div>
        <div class="status">
            <span class="dot {{ getColorForTableStatus($t->status) }}"></span>
        </div>
    </div>

    <table class="table w-50 mx-auto mb-5">
        <tbody class="view">
            <tr>
                <th>Table No</th>
                <td>{{ $t->id }}</td>
            </tr>
            <tr>
                <th>Seat</th>
                <td>{{ $t->seat }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ ucfirst($t->status) }}</td>
            </tr>
            <tr>
                <th>QR Code</th>
                <td>
                    <img src="data:image/png;base64, {!! base64_encode($qr) !!} " title="Click to download QR code">
                </td>
            </tr>
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            $('div.tableBox').css('border-color', $('span.dot').css('border-color'));

            $('td img').click(function() {
                window.location = '/tables/qr?table=' + '{{ $t->id }}'
            });
        });
    </script>
@endsection
