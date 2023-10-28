@extends('mgmtlayout')
@section('title', 'View Customer')

@section('body')
    <a href="/customers" class="btn btn-secondary mb-4" role="button">
        <i class="fas fa-arrow-left"></i>&nbsp; Back to index
    </a>

    <table class="table w-50 mx-auto mb-5">
        <tbody class="view">
            <tr>
                <th>ID</th>
                <td>{{ $c->id }}</td>
            </tr>
            <tr>
                <th>Name</th>
                <td>{{ $c->name }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $c->email }}</td>
            </tr>
            <tr>
                <th>Point</th>
                <td>{{ $c->point }}</td>
            </tr>
            <tr>
                <th>Joining Date</th>
                <td>{{ $c->created_at->format('d-m-Y H:i') }}</td>
            </tr>
        </tbody>
    </table>
@endsection
