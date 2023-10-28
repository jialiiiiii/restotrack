@extends('mgmtlayout')
@section('title', 'Staff')

@section('body')
    @if (session()->has('msg') && session()->get('msg') == 'deleteSuccess')
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Successful',
                text: 'Staff deleted successfully!',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
            })
        </script>
    @endif

    <a href="/staff/create" class="btn btn-secondary mb-4" role="button">
        <i class="fas fa-plus"></i>&nbsp; Add staff
    </a>

    <form>
        <div class="input-group w-25 mb-4 ms-auto">
            <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control search-bar" placeholder="ID, Name" name="search" maxlength="50"
                value="{{ $query }}" aria-describedby="basic-addon1">
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
                    <th class="text-center">Joining Date</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($staff as $s)
                    <tr>
                        <td class="text-center" scope="row">
                            {{ ($staff->currentPage() - 1) * $staff->perPage() + $loop->iteration }}</td>
                        <td class="text-center">{{ $s->id }}</td>
                        <td>{{ $s->name }}</td>
                        <td class="text-center">{{ $s->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <button class="btn-action" onclick="goTo('/staff/{{ $s->id }}')" title="View">
                                <i class="fas fa-eye btn-view"></i>
                            </button>
                            <button class="btn-action" onclick="goTo('/staff/{{ $s->id }}/edit')" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form class="delete-form d-inline" method="post" action="/staff/{{ $s->id }}"
                                data-name="{{ $s->name }}">
                                @csrf
                                @method('delete')

                                <button class="btn-action"><i class="fas fa-trash" title="Delete"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $staff->links('pagination::bootstrap-5') }}
    </div>
@endsection
