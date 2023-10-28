@extends('mgmtlayout')
@section('title', 'View Staff')

@section('body')
    <a href="/staff" class="btn btn-secondary mb-4" role="button">
        <i class="fas fa-arrow-left"></i>&nbsp; Back to index
    </a>

    <table class="table w-50 mx-auto mb-5">
        <tbody class="view">
            <tr>
                <th>ID</th>
                <td>{{ $s->id }}</td>
            </tr>
            <tr>
                <th>Name</th>
                <td>{{ $s->name }}</td>
            </tr>
            <tr>
                <th>Role</th>
                <td>{{ ucfirst($s->role) }}</td>
            </tr>
            <tr>
                <th>Joining Date</th>
                <td>{{ $s->created_at->format('d-m-Y H:i') }}</td>
            </tr>
        </tbody>
    </table>
@endsection
