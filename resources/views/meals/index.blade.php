@extends('mgmtlayout')
@section('title', 'Meals')

@section('body')
    @if (session()->has('msg'))
        <script>
            @if (session()->get('msg') == 'deleteSuccess')
                Swal.fire({
                    icon: 'success',
                    title: 'Successful',
                    text: 'Meal deleted successfully!',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                })
            @elseif (session()->get('msg') == 'toggleSuccess')
                Swal.fire({
                    icon: 'success',
                    title: 'Successful',
                    text: 'The meal is now {{ session()->get('result') }}.',
                    showCancelButton: true,
                    cancelButtonText: 'Close',
                    confirmButtonText: 'View',
                    confirmButtonColor: '#3085d6',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/meals/' + '{{ session()->get('id') }}';
                    }
                })
            @endif
        </script>
    @endif

    <a href="/meals/create" class="btn btn-secondary mb-4" role="button">
        <i class="fas fa-plus"></i>&nbsp; Add meal
    </a>

    <form>
        <div class="input-group w-25 mb-4 ms-auto">
            <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control search-bar" placeholder="ID, Name, Category, Price" name="search"
                maxlength="50" value="{{ $query }}" aria-describedby="basic-addon1">
            <span id="clear-icon" class="clear-icon"><i class="fas fa-times"></i></span>
        </div>
    </form>

    <div id="result" class="mb-4">
        <table class="table">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">ID</th>
                    <th>Name</th>
                    <th class="text-center">Category</th>
                    <th class="text-center">Price (RM)</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($meals as $m)
                    <tr>
                        <td class="text-center" scope="row">
                            {{ ($meals->currentPage() - 1) * $meals->perPage() + $loop->iteration }}</td>
                        <td class="text-center">{{ $m->id }}</td>
                        <td>{{ $m->name }}</td>
                        <td class="text-center">{{ $m->category }}</td>
                        <td class="text-center">
                            @if ($m->sales > 0)
                                <span class="text-decoration-line-through me-1">{{ number_format($m->price, 2) }}</span>
                                {{ number_format($m->price * (100 - $m->sales) / 100, 2) }}
                            @else
                                {{ number_format($m->price, 2) }}
                            @endif
                        </td>
                        <td class="text-center" style="min-width: 205px">
                            <form class="availability-form d-inline" method="post"
                                action="/meals/{{ $m->id }}/toggleAvailability" data-name="{{ $m->name }}"
                                data-availability="{{ $m->available }}">

                                @csrf
                                @method('PUT')

                                <button class="btn-action" title="Availability">
                                    @if ($m->available)
                                        <i class="fas fa-check-circle"></i>
                                    @else
                                        <i class="fas fa-times-circle"></i>
                                    @endif
                                </button>
                            </form>
                            <button class="btn-action" onclick="goTo('/meals/{{ $m->id }}')" title="View">
                                <i class="fas fa-eye btn-view"></i>
                            </button>
                            <button class="btn-action" onclick="goTo('/meals/{{ $m->id }}/edit')" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form class="delete-form d-inline" method="post" action="/meals/{{ $m->id }}"
                                data-name="{{ $m->name }}">
                                @csrf
                                @method('delete')

                                <button class="btn-action"><i class="fas fa-trash" title="Delete"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $meals->links('pagination::bootstrap-5') }}
    </div>

    <script>
        $(function() {
            // availability button
            $('.availability-form').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var name = form.data('name');
                var availability = form.data('availability');
                var message = 'Are you sure to make <strong>' + name + '</strong> ';

                console.log(availability);

                if (availability) {
                    message += 'not available?';
                } else {
                    message += 'available?';
                }

                Swal.fire({
                    icon: 'warning',
                    title: 'Caution',
                    html: message,
                    showCancelButton: true,
                    cancelButtonText: 'Cancel',
                    confirmButtonText: 'Yes',
                    confirmButtonColor: '#3085d6',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.off('submit'); // remove the submit event listener temporarily
                        form.submit(); // submit the form
                    }
                })
            });
        });
    </script>
@endsection
