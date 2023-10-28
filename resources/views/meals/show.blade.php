@extends('mgmtlayout')
@section('title', 'View Meal')

@section('head')
    <style>
        .img-thumbnail {
            width: 250px;
            height: 250px;
        }
    </style>
@endsection

@section('body')
    <a href="/meals" class="btn btn-secondary mb-4" role="button">
        <i class="fas fa-arrow-left"></i>&nbsp; Back to index
    </a>

    <table class="table w-50 mx-auto mb-5">
        <tbody class="view">
            <tr>
                <th>Image</th>
                <td><img src="/img/meals/{{ $m->id }}.png" alt="Meal Image" class="img-thumbnail"></td>
            </tr>
            <tr>
                <th>ID</th>
                <td>{{ $m->id }}</td>
            </tr>
            <tr>
                <th>Name</th>
                <td>{{ $m->name }}</td>
            </tr>
            <tr>
                <th>Category</th>
                <td>{{ $m->category }}</td>
            </tr>
            <tr>
                <th>Description</th>
                <td>{{ $m->description }}</td>
            </tr>
            <tr>
                <th>Price (RM)</th>
                <td>
                    @if ($m->sales > 0)
                        <span class="text-decoration-line-through me-1">{{ number_format($m->price, 2) }}</span>
                        {{ number_format(($m->price * (100 - $m->sales)) / 100, 2) }}
                    @else
                        {{ number_format($m->price, 2) }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>Sales (%)</th>
                <td>{{ $m->sales }}</td>
            </tr>
            <tr>
                <th>Sold (Units)</th>
                <td>{{ $m->sold }}</td>
            </tr>
            <tr>
                <th>Available</th>
                <td>{{ $m->available ? 'Yes' : 'No' }}</td>
            </tr>
        </tbody>
    </table>
@endsection
